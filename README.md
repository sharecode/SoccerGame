# SoccerGame
It's a Soccer Game
code from
http://www.thaiseoboard.com/index.php/topic,394020.0.html

alpha test 25/5/2560 -> https://drive.google.com/file/d/0B5kY_8EqszWwRjV0QkxnRGI4Slk/view?usp=sharing
demo => http://thaitipster.esy.es/ 

รหัสแอดมินเพื่อดูหลังบ้าน admin01:123456 ห้ามเปลี่ยนพาสแอดมินนะครับ 

 wanwan003

ขอแจก โค้ด php สำหรับทายผลบอล
สามารถ เอาไปใช้เอง พัฒนาต่อ โมขาย ได้หมดครับ 
เขียนมือแกะง่าย เหมาะสำหรับกลุ่ม หรือเว็บฟุตบอล เอาไปจัดกิจกรรมสนุกๆให้สมาชิก

คุณสมบัติทั่วไป พยายาม เลียนแบบสนามจริงให้ได้มากที่สุด
- ตัวนี้ทายบอลเดียวได้อย่างเดียว แต่ทายได้หลายคู่ต่อวันแล้วแต่จะเซตกันไป
- สามารถเปิดราคาให้ทายได้หลาย ราคา หลายรูปแบบ handicap over/under 1x2 ทั้งครึ่งแรก และ เต็มเวลา
- ออดซ์ที่ใช้ในระบบจะเป็น HK* เท่านั้นนะครับ 
- คะแนน ก็ตามออดซ์ที่เลือกทายกันเลยครับ

การติดตั้งนะครับ
- แตกไฟล์กันเลย
- import ไฟล์ install.sql เพื่อสร้างตารางฐานข้อมูล
- ไปที่ไฟล์ ext.php แก้ตัวแปลเพื่อเชื่อมต่อกับฐานข้อมูล
       $_userdb="";
       $_passdb="";
       $_host="localhost";
       $_db="";
   
- แก้ตัวแปล $_url ในไฟล์ ext.php และ main.js เพื่อ ให้ระบบโหลดรูป รีไดเรค โหลดไฟล์ได้ถูกต้องนะครับ ตัวอย่าง $_url="http://localhost/ "; ให้มี / ปิดท้ายด้วยนะครับ
- ที่ เทเบิล admin เพิ่ม id (id คือ ฟิลด์ id ใน เทเบิล member_) ด้วยคำสั่ง insert into admin values(x,0); x คือ ไอดี ใดๆ ที่อยากให้ใครเป็น แอดมิน จัดการหลังบ้านได้
- ที่ เทเบิล zean เพิ่ม ไอดี แอดมิน ที่สามารถให้ทีเด็ด ประจำวันได้ ที่เด็ดประจำวันหมายถึงทีเด็ดจากเว็บไซต์ ผู้ที่สามารถทำได้จะต้องเป็นแอดมิน
-ไปที่ mod.php เพื่อสร้างตารางทายผลกันเลยครับ

สงสัยตรงไหนถามไว้นะครับเดียวมาตอบ ระบบยังไม่เคยทดสอบใช้จริงบนเว็บไหนนะครับ
สนใจลอง เอาไปทดสอบบน localhost กันก่อนได้ครับ


เพิ่มเติมการ import html file เพื่อสร้างตาราง ทายผล
คุณสามารถ เซฟหน้า html ของเว็บใดเว็บหนึ่งมา แล้วใช้ php dom ไล่แกะราคา มาลงเว็บได้ โดยไม่ต้องมานั่งคีย์เอง แนะนำ isc w m อะไรพวกนี้ จะมีคู่บอลเปิดเยอะ บอลเล็กบอลน้อยเปิดหมด
ตัวนี้ ผมจะแนะนำ การimport ราคา จากเว็บ m88 
ให้ สมัคร สมาชิก เข้าไปที่หน้า msport 

ข้างบนให้เลือก main market ตรง ขวา มือให้เลือก HK (hong kong odds นั่นเอง)


เซฟ ไฟล์ แบบ สมบูรณ์ แล้วไปไล่ หาไฟล์ main2.htm
อับขึ้นเว็บกันเลย ถ้าหน้าเว็บ ยังไม่มีการแก้ไขอะไร php dom ของเราก็จะ แกะได้สำเร็จ และ สร้างตาราง มาให้เราเลือกเพิ่มเข้าในระบบ  wanwan019


พิเศษ 1 ท่าน สำหรับต้องการติดตั้งเพื่อใช้งานจริง ผมอาสา ลงให้และ แก้บัคทั้งหมดให้ เพื่อทดสอบการใช้งานในระดับ beta ครับ
