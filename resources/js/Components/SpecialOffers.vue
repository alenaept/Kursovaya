<script setup>
import Header from '@/Components/Header.vue';
import { ref, onMounted } from 'vue';

const offers = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchOffers = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        const response = await fetch('/api/special-offers');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Загружены акции:', data);
        offers.value = data;
    } catch (err) {
        console.error('Ошибка загрузки:', err);
        error.value = 'Не удалось загрузить акции';
    } finally {
        loading.value = false;
    }
};

const formatPercent = (percent) => {
    return `${percent}%`;
};

onMounted(() => {
    fetchOffers();
});
</script>

<template>
    <div>
        <Header />
        
        <div class="offers-page">
            <div class="container">
                <h1 class="page-title">Акции и скидки</h1>
                
                <!-- Загрузка -->
                <div v-if="loading" class="loading">
                    <div class="spinner"></div>
                    <p>Загрузка акций...</p>
                </div>
                
                <!-- Ошибка -->
                <div v-else-if="error" class="error">
                    <p>❌ {{ error }}</p>
                    <button @click="fetchOffers" class="retry-btn">Повторить</button>
                </div>
                
                <!-- Нет данных -->
                <div v-else-if="offers.length === 0" class="empty">
                    <p>📭 Нет активных акций</p>
                    <p class="hint">Скоро появятся новые предложения</p>
                </div>
                
                <!-- Карточки акций -->
                <div v-else class="offers-grid">
                    <div 
                        v-for="offer in offers" 
                        :key="offer.id" 
                        class="offer-card"
                    >
                        <div class="offer-badge">
                            <span class="percent">{{ offer.percent }}%</span>
                            <span class="label">скидка</span>
                        </div>
                        
                        <h3 class="offer-title">{{ offer.title }}</h3>
                        
                        <p class="offer-description">{{ offer.description }}</p>
                        
                        <button class="book-btn">Записаться на прием</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.offers-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: calc(100vh - 60px);
    padding: 60px 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.page-title {
    font-size: 42px;
    font-weight: 800;
    color: white;
    text-align: center;
    margin-bottom: 50px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.offers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
}

.offer-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    position: relative;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.offer-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.offer-badge {
    position: absolute;
    top: -15px;
    right: 20px;
    background: linear-gradient(135deg, #ff6b6b, #ee5a24);
    border-radius: 50px;
    padding: 10px 20px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.percent {
    font-size: 28px;
    font-weight: 800;
    color: white;
    display: block;
    line-height: 1;
}

.label {
    font-size: 12px;
    color: white;
    opacity: 0.9;
}

.offer-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin: 0 0 15px 0;
    padding-right: 80px;
}

.offer-description {
    font-size: 14px;
    color: #666;
    line-height: 1.6;
    margin-bottom: 25px;
}

.book-btn {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.book-btn:hover {
    transform: scale(1.02);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.loading, .error, .empty {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 20px;
    margin-top: 40px;
}

.loading {
    color: #666;
}

.error {
    color: #d32f2f;
    background: #ffebee;
}

.empty {
    color: #999;
}

.spinner {
    width: 50px;
    height: 50px;
    margin: 0 auto 20px;
    border: 4px solid #e0e0e0;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.retry-btn {
    margin-top: 20px;
    padding: 10px 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
}

.hint {
    font-size: 14px;
    margin-top: 10px;
    color: #bbb;
}

@media (max-width: 768px) {
    .offers-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .page-title {
        font-size: 32px;
    }
    
    .offer-card {
        padding: 25px;
    }
    
    .offer-title {
        font-size: 20px;
        padding-right: 70px;
    }
    
    .percent {
        font-size: 22px;
    }
}
</style>