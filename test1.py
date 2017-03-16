import serial
import modbus_tk
import modbus_tk.defines as cst
import psycopg2
import requests
from modbus_tk import modbus_rtu
from picamera import PiCamera
from datetime import datetime
from subprocess import call


PORT = '/dev/ttyACM0'


def main():
	"""main"""
	logger = modbus_tk.utils.create_logger("console")
	try:
		#Connect to the slave
		master = modbus_rtu.RtuMaster(
			serial.Serial(port=PORT, baudrate=115200, bytesize=8, parity='N', stopbits=1, xonxoff=0)
		)
		master.set_timeout(5.0)
		master.set_verbose(True)
		logger.info("connected")
		data = master.execute(17, cst.READ_INPUT_REGISTERS, 0, 1)
		print("Modbus Data : " + str(data[0]))
		
		
		pic_name = str(datetime.now().strftime('%Y-%m-%d_%H:%M:%S'))
		camera = PiCamera()
		camera.capture('pic/' + pic_name + '.jpg')

		call('git add pic/' + pic_name + '.jpg', shell=True)
		call('git commit -m "add picture"', shell=True)
		call('git push origin master', shell=True)

		data_w = requests.get('http://api.wunderground.com/api/a6be6269233f1bc8/conditions/astronomy/q/TH/Bangkok.json').json()
		date = data_w['current_observation']['local_time_rfc822']
		print(date)
		temp = data_w['current_observation']['temp_c']
		print(temp)
		weather = data_w['current_observation']['weather']
		print(weather)
		pressure = data_w['current_observation']['pressure_mb']
		print(pressure)
		conn_string = "host='ec2-54-221-255-153.compute-1.amazonaws.com' dbname='dd6j72nr8uanuq' user='krdookwgbudwkq' password='337d29bb2b87f471b47f286fcb7fa1fb885b4b063f9ea5197805f4f679e7d9b8'"
		conn = psycopg2.connect(conn_string)
		with conn:
			cur = conn.cursor()
			cur.execute("""INSERT INTO WEATHER_HUMIDITY( date_c, temp, weather, air_p, hum, pic ) VALUES (%s, %s, %s, %s, %s, %s)""",(str(date), str(temp), str(weather), str(pressure), str(data[0]), str(pic_name)))
			conn.commit()
		if conn:
			conn.close()
		
	except modbus_tk.modbus.ModbusError as exc:
		logger.error("%s- Code=%d", exc, exc.get_exception_code())

		
if __name__ == "__main__":
	main()
