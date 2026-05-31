<script setup>
import { ref } from 'vue'
import axios from 'axios'

const form = ref({ name: '', phone: '' })
const status = ref(null)

const submitForm = async () => {
  try {
    await axios.post('/api/requests', form.value)
    status.value = 'Заявка отправлена!'
    form.value = { name: '', phone: '' } 
  } catch (err) {
    status.value = 'Ошибка при отправке'
  }
}
</script>

<template>
  <div class="contact-section">
    <div class="info-card">
      <p>Клиника Happy Teeth</p>
      <p>Адрес: 30 микрорайон, дом 3</p>
      <p>Телефон:  +7 (3052) 58-42-95 </p>
      <p>E-mail: Happyteethclinic@mail.ru </p>
      <p>Режим работы: пн-вск с 9-00 до 20-00</p>
      <p>Главный врач: Алексей Смирнов</p>
      <p>Директор: Ольга Никитина</p>
      <p>Юр. адрес: 666969, Россия, Иркутская область,  г. Иркутск, мкр. 10, 50/50а</p>
    </div>


    <div class="form-card">
      <h3>Запишитесь к нам на консультацию</h3>
      <form @submit.prevent="submitForm">
        <input v-model="form.name" @input="form.name = form.name.replace(/[^a-zA-Zа-яА-ЯёЁ\s]/g, '')" type="text" placeholder="Как к вам обращаться?"  required>
        <input v-model="form.phone" @input="form.phone = form.phone.replace(/\D/g, '')" type="tel" placeholder="Ваш телефон" maxlength="11" required>
        <button type="submit">Записаться</button>
      </form>
      <p v-if="status" class="status-msg">{{ status }}</p>
    </div>
  </div>
</template>

<style scoped>
.contact-section {
  display: flex;
  gap: 40px;
  max-width: 1050px;
  margin: 20px auto;
  font-family: sans-serif;
}

.info-card {
  flex: 1;
  padding: 30px;
  border: 3px solid #A4C3DD;
  border-radius: 15px;
  font-size: 17px;
  
}
h3{
    
    font-size: 28px;
}
p{
    margin-bottom: 5px;
     margin-top: 3px;
}

.form-card { 
    margin-top: 20px;
    text-align: center;
    width: 450px;
 }

input {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border: 2px solid #ccc;
  border-radius: 10px;
}

button {
    margin-top: 10px;
  width: 100%;
  padding: 12px;
  background: #265B87;
  color: white;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: bold;
}

.status-msg { margin-top: 15px; color: rgb(0, 3, 192); }
</style>
