<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import { ref, onMounted } from 'vue';

const offers = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchOffers = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        const response = await fetch('/api/public/special-offers');
        
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
                <h5>Для получения скидки необходимо предоставить документы на кассе после посещения, либо посетить стоматолога, пока акция действует</h5>
                
                <div v-if="loading" class="loading">
                    <p>Загрузка акций...</p>
                </div>
                
                <div v-else-if="error" class="error">
                    <p>{{ error }}</p>
                    <button @click="fetchOffers" class="retry-btn">Повторить</button>
                </div>
                
                <div v-else-if="offers.length === 0" class="empty">
                    <p>Нет активных акций</p>
                </div>
                
                <div v-else class="offers-grid">
                    <div 
                        v-for="offer in offers" 
                        :key="offer.id_specialOffers || offer.id" 
                        class="offer-card"
                    >
                        <div class="offer-badge">-{{ offer.percent }}%</div>
                        <h3 class="offer-title">{{ offer.title }}</h3>
                        <p class="offer-description">{{ offer.desc || offer.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Footer />
</template>

<style scoped>

.offers-page {
    background: white;
    padding: 30px 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.page-title {
    font-size: 25px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 5px;
}

h5 {
    text-align: center;
    margin-bottom: 30px;
}

.offers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
}

.offer-card {
    background: white;
    border: 3px solid #A4C3DD;
    border-radius: 20px;
    padding: 30px;
    position: relative;
    transition: all 0.3s ease;
}

.offer-card:hover {
    background-color: #EEF7FF;
}

.offer-title {
    font-size: 24px;
    font-weight: bold;
    margin: 0 0 15px 0;
    padding-right: 80px;
}

.offer-description {
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 25px;
}


.offer-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    background: #265B87;
    color: white;
    padding: 4px 10px;
    border-radius: 30px;
    font-weight: bold;
    font-size: 18px;
}

.loading {
    text-align: center;
    color: #666;
    padding: 60px;
}

.error {
    text-align: center;
    padding: 60px;
    color: #dc2626;
}

.empty {
    text-align: center;
    padding: 60px;
    color: #64748b;
}

.retry-btn {
    margin-top: 15px;
    padding: 8px 20px;
    background: #265B87;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.retry-btn:hover {
    background: #1e4a6e;
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
}
</style>