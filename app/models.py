from sqlalchemy import Column, Integer, String
from app.database import Base

class Order(Base):
    __tablename__ = "orders"

    id = Column(Integer, primary_key=True, index=True)
    item_id = Column(Integer)
    quantity = Column(Integer)
    branch_id = Column(Integer)
    status = Column(String, default="pending")