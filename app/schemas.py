from pydantic import BaseModel

class OrderCreate(BaseModel):
    item_id: int
    quantity: int

class OrderResponse(OrderCreate):
    id: int
    status: str

    class Config:
        from_attributes = True