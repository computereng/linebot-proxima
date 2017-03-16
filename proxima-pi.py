import serial
import modbus_tk
import modbus_tk.defines as cst
import psycopg2
import requests
import time
from modbus_tk import modbus_rtu
from picamera import PiCamera
from datetime import datetime
from subprocess import call


PORT = '/dev/ttyACM0'


def main():
	"""main"""
	logger = modbus_tk.utils.create_logger("console")
	master = modbus_rtu.RtuMaster(
		serial.Serial(port=PORT, baudrate=115200, bytesize=8, parity='N', stopbits=1, xonxoff=0)
	)
	master.set_timeout(5.0)
	master.set_verbose(True)
	camera = PiCamera()
	conn_string = "host='ec2-75-101-142-182.compute-1.amazonaws.com' dbname='d5mmu71c2lbm9o' user='flghpbnnuhfevu' password='835ecb49bf0c74bc09716dbecdd8aa5df0ff7fa84bde3876dba031b27d632abf'"
	conn = psycopg2.connect(conn_string)
	cur = conn.cursor()
	while(True):
		try:
			#Connect to the slave
			
			logger.info("connected")
			data = master.execute(17, cst.READ_INPUT_REGISTERS, 0, 1)
			print("Modbus Data : " + str(data[0]))

			pic_name = str(datetime.now().strftime('%Y-%m-%d_%H:%M:%S'))
			camera.capture('pic/' + pic_name + '.jpg')

			call('sudo git add pic/' + pic_name + '.jpg', shell=True)
			call('sudo git commit -m "add picture"', shell=True)
			call('sudo git push origin master', shell=True)

			data_w = requests.get('http://api.wunderground.com/api/a6be6269233f1bc8/conditions/astronomy/q/TH/Bangkok.json').json()
			date = data_w['current_observation']['local_time_rfc822']
			print(date)
			temp = data_w['current_observation']['temp_c']
			print(temp)
			weather = data_w['current_observation']['weather']
			print(weather)
			pressure = data_w['current_observation']['pressure_mb']
			print(pressure)
			
			with conn:
				cur.execute("""INSERT INTO WEATHER_HUMIDITY( date, tempc, weather, pressure, humidity, image ) VALUES (%s, %s, %s, %s, %s, %s)""",(str(date), str(temp), str(weather), str(pressure), str(data[0]), str(pic_name)))
				conn.commit()

		except modbus_tk.modbus.ModbusError as exc:
			logger.error("%s- Code=%d", exc, exc.get_exception_code())

		time.sleep(30)

		
if __name__ == "__main__":
	main()
