from sqlalchemy import create_engine, Column, Integer, String, Float, DateTime, ForeignKey, JSON
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
import datetime

Base = declarative_base()

class User(Base):
    __tablename__ = 'users'
    id = Column(Integer, primary_key=True)
    username = Column(String, unique=True)
    face_embedding = Column(JSON)  # Store face embeddings for comparison
    total_points = Column(Integer, default=0)

class Bin(Base):
    __tablename__ = 'bins'
    id = Column(Integer, primary_key=True)
    name = Column(String)
    lat = Column(Float)
    long = Column(Float)
    organic_level = Column(Float, default=0.0)
    inorganic_level = Column(Float, default=0.0)
    last_update = Column(DateTime, default=datetime.datetime.utcnow)
    predicted_full_time = Column(DateTime, nullable=True)

class PollutionLog(Base):
    __tablename__ = 'pollution_logs'
    id = Column(Integer, primary_key=True)
    bin_id = Column(Integer, ForeignKey('bins.id'))
    mq135_value = Column(Integer)
    timestamp = Column(DateTime, default=datetime.datetime.utcnow)

# Database Setup
engine = create_engine('sqlite:///meta_bin_go.db')
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

def init_db():
    Base.metadata.create_all(bind=engine)
