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
  
    <div class="form-card">
      <h3>Запишитесь к нам на консультацию</h3>
      <form @submit.prevent="submitForm">
        <input v-model="form.name" @input="form.name = form.name.replace(/[^a-zA-Zа-яА-ЯёЁ\s]/g, '')" type="text" placeholder="Как к вам обращаться?"  required>
        <input v-model="form.phone" @input="form.phone = form.phone.replace(/\D/g, '')" type="tel" placeholder="Ваш телефон" maxlength="11" required>
        <button type="submit">Записаться</button>
      </form>
      <p v-if="status" class="status-msg">{{ status }}</p>
    </div>
 
</template>

<style scoped>

h3{
    
    font-size: 28px;
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