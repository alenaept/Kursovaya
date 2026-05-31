<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import { ref, onMounted } from 'vue';

const groupedPrices = ref({});
const loading = ref(true);
const error = ref(null);

const fetchPrices = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        const response = await fetch('/api/public/prices/grouped');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Данные от API:', data);
        console.log('Тип данных:', typeof data);
        console.log('Количество категорий:', Object.keys(data).length);
        
        // Проверяем, что данные не пустые
        if (data && typeof data === 'object') {
            groupedPrices.value = data;
        } else {
            groupedPrices.value = {};
        }
    } catch (err) {
        console.error('Ошибка загрузки:', err);
        error.value = 'Не удалось загрузить цены';
        groupedPrices.value = {};
    } finally {
        loading.value = false;
    }
};

const formatPrice = (price) => {
    if (!price) return '0 ₽';
    const num = parseFloat(price);
    if (isNaN(num)) return price;
    return num.toLocaleString('ru-RU') + ' ₽';
};

onMounted(() => {
    fetchPrices();
});
</script>

<template>
    <div>
        <Header />
        
        <div class="prices-page">
            <div class="container">
                <h1 class="page-title">Цены на услуги</h1>
                
                <div v-if="loading" class="loading">
                    <div class="spinner"></div>
                    <p>Загрузка цен...</p>
                </div>
                
                <div v-else-if="error" class="error">
                    <p>{{ error }}</p>
                    <button @click="fetchPrices" class="retry-btn">Повторить</button>
                </div>
                
                <div v-else-if="Object.keys(groupedPrices).length === 0" class="empty">
                    <p>Нет данных о ценах</p>
                    <pre style="font-size: 12px; margin-top: 10px;">{{ groupedPrices }}</pre>
                </div>
                
                <div v-else>
                    <div 
                        v-for="(items, category) in groupedPrices" 
                        :key="category" 
                        class="category-section"
                    >
                        <h2 class="category-title">{{ category }}</h2>
                        
                        <div class="prices-list">
                            <div 
                                v-for="(item, index) in items" 
                                :key="item.id || index" 
                                class="price-row"
                            >
                                <span class="service-name">{{ item.description }}</span>
                                <span class="service-price">{{ formatPrice(item.price) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Footer />
</template>

<style scoped>
.prices-page {
    padding: 30px 20px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

.page-title {
    text-align: center;
    font-size: 25px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #1e293b;
}

.category-section {
    margin-bottom: 20px;
    background: white;
    border-radius: 16px;
    padding: 20px;
}

.category-title {
    font-size: 24px;
    font-weight: 600;
    color: #265B87;
    padding-bottom: 10px;
    border-bottom: 2px solid #A4C3DD;
}

.prices-list {
    display: flex;
    flex-direction: column;
}

.price-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding: 14px 0;
    border-bottom: 1px solid #f0f0f0;
}

.price-row:last-child {
    border-bottom: none;
}

.service-name {
    font-size: 16px;
    line-height: 1.4;
    flex: 1;
    padding-right: 20px;
    color: #334155;
}

.service-price {
    font-size: 18px;
    font-weight: 700;
    color: #265B87;
    white-space: nowrap;
}

.loading, .error, .empty {
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

.error {
    color: #dc2626;
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
    .prices-page {
        padding: 20px 16px;
    }
    
    .page-title {
        font-size: 24px;
        margin-bottom: 30px;
    }
    
    .category-title {
        font-size: 20px;
    }
    
    .price-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .service-name {
        padding-right: 0;
    }
    
    .service-price {
        align-self: flex-end;
    }
}
</style>