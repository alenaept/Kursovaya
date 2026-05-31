<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Состояния
const reviews = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchReviews = async () => {
  try {
    loading.value = true;

    const response = await axios.get('/api/public/reviews');
    reviews.value = response.data;
    error.value = null;
  } catch (err) {
    console.error('Ошибка при загрузке отзывов:', err);
    error.value = 'Не удалось загрузить отзывы. Пожалуйста, попробуйте позже.';
  } finally {
    loading.value = false;
  }
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('ru-RU');
};

onMounted(() => {
  fetchReviews();
});
</script>

<template>
  <div>
    <Header />

    <div class="reviews-page">
      <div class="container">
        <h1>Отзывы наших клиентов</h1>

        <div v-if="error" class="error-state">
          <svg class="error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p>{{ error }}</p>
          <button @click="fetchReviews" class="retry-btn">Попробовать снова</button>
        </div>

        <div v-else-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Загрузка отзывов...</p>
        </div>

        <div v-else-if="reviews.length > 0" class="reviews-grid">
          <div 
            v-for="review in reviews" 
            :key="review.id" 
            class="review-card"
          >
            <div class="review-header">
              <h3 class="user-name">{{ review.user_name || 'Пользователь' }}</h3>
              <span class="review-date">{{ formatDate(review.created_at) }}</span>
            </div>
            <div class="review-content">
              <p class="review-text">{{ review.text }}</p>
            </div>
          </div>
        </div>

        <div v-else class="empty-state">
          <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p>Пока нет отзывов</p>
        </div>
      </div>
    </div>
  </div>
  <Footer />
</template>

<style scoped>

.container {
  max-width: 1200px;
  margin: 0 auto;
}

h1 {
  margin-top: 30px;
  text-align: center;
  font-size: 25px;
  font-weight: 700;
  margin-bottom: 40px;
  padding-bottom: 15px;
}

.reviews-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 24px;
}

.review-card {
  border-radius: 16px;
  border: 3px solid #A4C3DD;

  width: 100%;
}

.review-card:hover {
  background-color: #e0edff;
}

.user-name {
  margin-left: 20px;
  margin-top: 10px;
  font-size: 18px;
  font-weight: 600;
}

.review-date {
  margin-left: 20px;
  font-size: 12px;
}

.review-content {
  padding: 20px;
}

.review-text {
  margin: 0;
  line-height: 1.6;
  word-break: break-word;
  font-size: 17px;
}



.empty-state {
  text-align: center;
  padding: 80px 20px;
  background: white;
  border-radius: 16px;
}


.empty-state p {
  font-size: 18px;
  color: #4a5568;
  margin: 10px 0;
}


.loading-state {
  text-align: center;
  padding: 60px 20px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e2e8f0;
  border-top-color: #265B87;
  border-radius: 50%;
  margin: 0 auto 15px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.retry-btn {
  background: #265B87;
  color: white;
  border: none;
  padding: 10px 24px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
}

.retry-btn:hover {
  background: #1e4a6e;
}


@media (max-width: 768px) {
  .reviews-page {
    padding: 20px 16px;
  }
  
  .page-title {
    font-size: 1.8rem;
    margin-bottom: 30px;
  }
  
  .reviews-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .user-avatar {
    width: 48px;
    height: 48px;
    font-size: 18px;
  }
  
  .user-name {
    font-size: 16px;
  }
}

@media (max-width: 480px) {
  .review-header {
    padding: 16px;
  }
  
  .review-content {
    padding: 16px;
  }
  
  .review-text {
    font-size: 14px;
  }
}
</style>