# Postman Examples for Hazard API

## GET /api/hazards

Method: GET
URL: http://127.0.0.1:8000/api/hazards

Headers:
- Accept: application/json

## POST /api/hazards

Method: POST
URL: http://127.0.0.1:8000/api/hazards

Headers:
- Content-Type: application/json
- Accept: application/json

Body (JSON):
{
  "username": "Ali",
  "category": "Road Hazard",
  "description": "Large pothole near the school entrance.",
  "latitude": 6.45,
  "longitude": 100.28,
  "reported_at": "2026-07-11 20:15:00",
  "device_info": "Android 15"
}

## DELETE /api/hazards/{id}

Method: DELETE
URL: http://127.0.0.1:8000/api/hazards/1

Headers:
- Accept: application/json
