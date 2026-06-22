import httpx
import os
from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from app.database import SessionLocal
from app.models import Order
from app.schemas import OrderCreate
from app.auth import verify_token
from app.rabbitmq import send_message

router = APIRouter()

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

@router.post("/orders")
def create_order(
    order: OrderCreate,
    payload=Depends(verify_token),
    db: Session = Depends(get_db)
):
    new_order = Order(
        item_id=order.item_id,
        quantity=order.quantity,
        branch_id=1
    )

    db.add(new_order)
    db.commit()
    db.refresh(new_order)

    # Kirim payload ke RabbitMQ
    send_message({
        "item_id": order.item_id,
        "branch_id": 1,
        "quantity_change": -order.quantity
    })

    # Panggil Inventory Service
    inventory_payload = {
        "item_id": order.item_id,
        "branch_id": 1,
        "quantity_change": -order.quantity
    }

    try:
        response = httpx.post(
            os.getenv("INVENTORY_SERVICE_URL"),
            json=inventory_payload
        )
        inventory_resp = response.json()
    except Exception as e:
        inventory_resp = {"error": f"Gagal menghubungi Inventory Service: {str(e)}"}

    return {
        "message": "Order Created",
        "order_id": new_order.id,
        "inventory_response": inventory_resp
    }

@router.get("/orders")
def get_orders(
    payload=Depends(verify_token),
    db: Session = Depends(get_db)
):
    return db.query(Order).all()