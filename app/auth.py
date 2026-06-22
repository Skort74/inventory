import os
from jose import jwt
from fastapi import Depends, HTTPException
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials

# Mengambil secret key dari .env
SECRET_KEY = os.getenv("JWT_SECRET_KEY", "sangat_rahasia_dan_panjang_sekali_32_karakter_")
ALGORITHM = "HS256"

security = HTTPBearer()

def verify_token(credentials: HTTPAuthorizationCredentials = Depends(security)):
    token = credentials.credentials
    
    try:
        # Tambahkan options untuk MENGABAIKAN verifikasi penerbit (issuer)
        # karena token ini di-generate oleh Laravel, bukan FastAPI.
        payload = jwt.decode(
            token, 
            SECRET_KEY, 
            algorithms=[ALGORITHM],
            options={
                "verify_iss": False, 
                "verify_aud": False,
                "verify_sub": False
            }
        )
        
        claims = payload.get("https://hasura.io/jwt/claims", {})
        
        if not claims:
            raise HTTPException(status_code=401, detail="Klaim Hasura tidak ditemukan di dalam token")

        return {
            "user_id": claims.get("x-hasura-user-id"),
            "branch_id": claims.get("x-hasura-branch-id"),
            "role": claims.get("x-hasura-default-role")
        }
        
    except jwt.ExpiredSignatureError:
        raise HTTPException(status_code=401, detail="Token JWT sudah kedaluwarsa. Silakan login ulang.")
    except jwt.JWTClaimsError as e:
        # Menangkap error spesifik jika ada klaim lain yang ditolak
        raise HTTPException(status_code=401, detail=f"Klaim token ditolak: {str(e)}")
    except jwt.JWTError as e:
        raise HTTPException(status_code=401, detail=f"Tanda tangan token tidak valid: {str(e)}")
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Terjadi kesalahan saat verifikasi: {str(e)}")