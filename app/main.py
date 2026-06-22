from fastapi import FastAPI
from app.database import Base, engine
from app.routes import orders, transaction

# Mencoba membuat tabel (Aman untuk dev)
try:
    Base.metadata.create_all(bind=engine)
except Exception as e:
    print(f"Gagal inisialisasi tabel: {e}")

app = FastAPI(title="Procurement Service")

# Routing dengan Prefix agar lebih rapi di Postman/Swagger
app.include_router(orders.router, prefix="/api")
app.include_router(transaction.router, prefix="/api")

@app.get("/")
def health_check():
    return {"status": "Procurement Service is Running Smoothly"}