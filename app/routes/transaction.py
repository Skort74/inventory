import httpx
import os
from fastapi import APIRouter, HTTPException
from pydantic import BaseModel

router = APIRouter()

class TransactionPayload(BaseModel):
    item_id: int
    branch_id: int
    quantity_change: int

@router.post("/process-transaction")
async def process_transaction(payload: TransactionPayload):
    HASURA_URL = os.getenv("HASURA_GRAPHQL_ENDPOINT")
    HASURA_SECRET = os.getenv("HASURA_ADMIN_SECRET")

    mutation_query = """
    mutation UpdateStokLive(
      $branch_id: Int!,
      $item_id: Int!,
      $quantity_change: Int!
    ) {
      update_inventories(
        where: {
          branch_id: {_eq: $branch_id},
          item_id: {_eq: $item_id}
        },
        _inc: {
          quantity: $quantity_change
        }
      ) {
        affected_rows
      }
    }
    """

    headers = {
        "Content-Type": "application/json",
        "x-hasura-admin-secret": HASURA_SECRET
    }

    variables = {
        "branch_id": payload.branch_id,
        "item_id": payload.item_id,
        "quantity_change": payload.quantity_change
    }

    async with httpx.AsyncClient() as client:
        try:
            response = await client.post(
                HASURA_URL,
                json={
                    "query": mutation_query,
                    "variables": variables
                },
                headers=headers
            )
            response.raise_for_status() # Lempar error jika status HTTP bukan 2xx
            return response.json()
        except httpx.ConnectError:
            raise HTTPException(
                status_code=500, 
                detail=f"Koneksi ditolak. Pastikan HASURA_GRAPHQL_ENDPOINT di .env adalah nama service Docker."
            )