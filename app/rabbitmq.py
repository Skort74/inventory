import pika
import json
import os

def send_message(message: dict):
    try:
        connection = pika.BlockingConnection(
            pika.ConnectionParameters(
                host=os.getenv("RABBITMQ_HOST", "rabbitmq")
            )
        )
        channel = connection.channel()
        queue_name = os.getenv("RABBITMQ_QUEUE", "procurement_queue")
        
        channel.queue_declare(queue=queue_name)
        
        channel.basic_publish(
            exchange='',
            routing_key=queue_name,
            body=json.dumps(message)
        )
        connection.close()
    except Exception as e:
        print(f"Peringatan: Gagal mengirim pesan ke RabbitMQ: {e}")