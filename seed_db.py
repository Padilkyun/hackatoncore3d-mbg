from database_models import init_db, SessionLocal, Bin

def seed_data():
    init_db()
    db = SessionLocal()
    
    # Check if bins already exist
    if db.query(Bin).count() == 0:
        # Seed some initial bins
        bins = [
            Bin(name="Bin A - Gate 1", lat=-6.200000, long=106.816666),
            Bin(name="Bin B - Cafeteria", lat=-6.210000, long=106.826666),
            Bin(name="Bin C - Parking Area", lat=-6.220000, long=106.836666),
        ]
        db.add_all(bins)
        db.commit()
        print("Database initialized and seeded with bins.")
    else:
        print("Database already exists.")
    db.close()

if __name__ == "__main__":
    seed_data()
