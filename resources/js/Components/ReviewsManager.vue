<template>
  <div class="admin-section">
    <div class="section-header">
      <h2>Отзывы (модерация)</h2>
    </div>

    <!-- Ожидают модерации -->
    <div class="review-category">
      <h3 class="pending-title">⏳ На модерации ({{ pendingReviews.length }})</h3>
      <div v-for="review in pendingReviews" :key="review.id_reviews" class="review-card pending">
        <div class="review-content">
          <p class="review-text">{{ review.review_text }}</p>
          <div class="review-meta">
            <span>Пользователь ID: {{ review.Users_idUser }}</span>
            <span>{{ formatDate(review.created_at) }}</span>
          </div>
        </div>
        <div class="review-actions">
          <button @click="approveReview(review)" class="btn-approve">✓ Опубликовать</button>
          <button @click="rejectReview(review)" class="btn-reject">✗ Удалить</button>
        </div>
      </div>
      <div v-if="pendingReviews.length === 0" class="empty-state">Нет отзывов на модерации</div>
    </div>

    <!-- Опубликованные -->
    <div class="review-category">
      <h3 class="published-title">✓ Опубликованные ({{ publishedReviews.length }})</h3>
      <div v-for="review in publishedReviews" :key="review.id_reviews" class="review-card published">
        <div class="review-content">
          <p class="review-text">{{ review.review_text }}</p>
          <div class="review-meta">
            <span>Пользователь ID: {{ review.Users_idUser }}</span>
            <span>{{ formatDate(review.created_at) }}</span>
          </div>
        </div>
        <div class="review-actions">
          <button @click="deletePublishedReview(review)" class="btn-delete-sm">Удалить</button>
        </div>
      </div>
      <div v-if="publishedReviews.length === 0" class="empty-state">Нет опубликованных отзывов</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ReviewsManager',
  data() {
    return {
      pendingReviews: [],
      publishedReviews: []
    }
  },
  mounted() {
    this.loadReviews()
  },
  methods: {
    formatDate(date) {
      if (!date) return '-'
      return new Date(date).toLocaleString('ru-RU')
    },
    async loadReviews() {
      try {
        const [pending, published] = await Promise.all([
          fetch('/api/reviews/pending', { headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` } }),
          fetch('/api/reviews/published', { headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` } })
        ])
        this.pendingReviews = await pending.json()
        this.publishedReviews = await published.json()
      } catch (error) {
        console.error('Ошибка загрузки:', error)
      }
    },
    async approveReview(review) {
      try {
        await fetch(`/api/reviews/${review.id_reviews}/approve`, {
          method: 'PUT',
          headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        })
        this.loadReviews()
      } catch (error) {
        console.error('Ошибка публикации:', error)
      }
    },
    async rejectReview(review) {
      if (confirm('Удалить этот отзыв без публикации?')) {
        try {
          await fetch(`/api/reviews/${review.id_reviews}/reject`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
          })
          this.loadReviews()
        } catch (error) {
          console.error('Ошибка удаления:', error)
        }
      }
    },
    async deletePublishedReview(review) {
      if (confirm('Удалить опубликованный отзыв?')) {
        try {
          await fetch(`/api/reviews/${review.id_reviews}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
          })
          this.loadReviews()
        } catch (error) {
          console.error('Ошибка удаления:', error)
        }
      }
    }
  }
}
</script>

<style scoped>
.review-category {
  margin-bottom: 30px;
}
.pending-title {
  color: #d97706;
  font-size: 18px;
  margin-bottom: 15px;
  padding-bottom: 8px;
  border-bottom: 2px solid #fef3c7;
}
.published-title {
  color: #059669;
  font-size: 18px;
  margin-bottom: 15px;
  padding-bottom: 8px;
  border-bottom: 2px solid #d1fae5;
}
.review-card {
  background: #f9fafb;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 12px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}
.review-card.pending {
  border-left: 4px solid #f59e0b;
}
.review-card.published {
  border-left: 4px solid #10b981;
}
.review-content {
  flex: 1;
}
.review-text {
  margin: 0 0 8px 0;
  color: #374151;
}
.review-meta {
  font-size: 12px;
  color: #6b7280;
  display: flex;
  gap: 16px;
}
.review-actions {
  display: flex;
  gap: 8px;
  margin-left: 16px;
}
.btn-approve {
  background: #10b981;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
}
.btn-reject, .btn-delete-sm {
  background: #ef4444;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
}
.empty-state {
  text-align: center;
  padding: 30px;
  color: #9ca3af;
  background: #f9fafb;
  border-radius: 8px;
}
</style>