<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import { ref, onMounted } from 'vue'
import axios from 'axios'

const doctors = ref([])
const loading = ref(false)
const error = ref(null)

const getPhotoUrl = (photoPath) => {
  if (!photoPath) {
    return '/storage/default-avatar.png'
  }
  
  if (photoPath.startsWith('http')) {
    return photoPath
  }
  
  if (photoPath.startsWith('/storage')) {
    return photoPath
  }
  
  return `/${photoPath}`
}

const getYearsWord = (years) => {
  const lastDigit = years % 10
  const lastTwoDigits = years % 100
  
  if (lastTwoDigits >= 11 && lastTwoDigits <= 14) {
    return 'лет'
  }
  
  if (lastDigit === 1) {
    return 'год'
  }
  
  if (lastDigit >= 2 && lastDigit <= 4) {
    return 'года'
  }
  
  return 'лет'
}

const handleImageError = (event) => {
  event.target.src = '/storage/default-avatar.png'
}

const fetchDoctors = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await axios.get('/api/public/doctors')
    doctors.value = response.data
  } catch (err) {
    console.error('Ошибка загрузки:', err)
    error.value = 'Не удалось загрузить список специалистов'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchDoctors()
})
</script>

<template>
  <div>
    <Header />
    
    <div class="specialists-page">
      <div class="container">
        <h1>Наши специалисты</h1>
        
        <div v-if="loading" class="loading-state">
          Загрузка специалистов...
        </div>
        
        <div v-else-if="error" class="error-state">
          {{ error }}
        </div>
        
        <div v-else-if="doctors.length === 0" class="empty-state">
          <p>Нет данных о специалистах</p>
        </div>
        
        <div v-else class="specialists-grid">
          <div 
            v-for="doctor in doctors" 
            :key="doctor.id_doctor" 
            class="specialist-card"
          >
            <div class="card-image">
              <img 
                :src="getPhotoUrl(doctor.photo_url)" 
                :alt="`${doctor.first_name} ${doctor.last_name}`"
                @error="handleImageError"
              >
            </div>
            
            <div class="card-content">
              <h3 class="doctor-name">
                {{ doctor.first_name }} {{ doctor.last_name }}
              </h3>
              
              <p class="doctor-specialization">{{ doctor.specialization || 'Стоматолог' }}</p>
              
              <div class="doctor-experience">
                Стаж: {{ doctor.experience_years || 0 }} {{ getYearsWord(doctor.experience_years || 0) }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Footer />
  </div>
</template>

<style scoped>
.specialists-page {
  padding: 30px 0;
  background: #fff;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

h1 {
  text-align: center;
  font-size: 25px;
  font-weight: 700;
  margin-bottom: 50px;
  color: #1e293b;
}

.specialists-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 30px;
}

.specialist-card {
  background: white;
  border-radius: 16px;
  border: 2px solid #A4C3DD;
  overflow: hidden;
}

.card-image {
  width: 100%;
  height: 280px;
  overflow: hidden;
  background: #f1f5f9;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.card-content {
  padding: 20px;
  text-align: center;
}

.doctor-name {
  font-size: 18px;
  font-weight: 700;
  margin: 0 0 8px 0;
  color: #1e293b;
}

.doctor-specialization {
  font-size: 14px;
  color: #265B87;
  margin-bottom: 8px;
}

.doctor-experience {
  font-size: 14px;
  color: #64748b;
}

.loading-state, .error-state, .empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #64748b;
}

.error-state {
  color: #dc2626;
}


@media (max-width: 1024px) {
  .specialists-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .specialists-grid {
    grid-template-columns: 1fr;
  }
  
  .card-image {
    height: 240px;
  }
  
  h1 {
    font-size: 28px;
  }
}
</style>