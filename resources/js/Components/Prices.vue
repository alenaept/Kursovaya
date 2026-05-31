<template>
    <div class="prices-page">
        <div v-if="loading" class="loading">Загрузка...</div>
        
        <div v-else-if="error" class="error">{{ error }}</div>
        
        <div v-else>
            <div 
                v-for="(items, category) in groupedPrices" 
                :key="category" 
                class="category-block"
            >
                <h2 class="category-title">{{ category }}</h2>
                
                <div class="services-list">
                    <div 
                        v-for="item in items" 
                        :key="item.id" 
                        class="service-row"
                    >
                        <span class="service-name">{{ item.description }}</span>
                        <span class="service-price">{{ item.price }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            groupedPrices: {},
            loading: true,
            error: null
        }
    },
    
    mounted() {
        this.fetchPrices();
    },
    
    methods: {
        async fetchPrices() {
            try {
                const response = await axios.get('/api/prices/grouped');
                this.groupedPrices = response.data;
                this.loading = false;
            } catch (err) {
                console.error('Ошибка загрузки:', err);
                this.error = 'Не удалось загрузить цены';
                this.loading = false;
            }
        }
    }
}
</script>

<style scoped>
.prices-page {
    max-width: 1000px;
    margin: 0 auto;
    padding: 40px 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.category-block {
    margin-bottom: 48px;
}

.category-title {
    font-size: 28px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 24px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e0e0e0;
}

.services-list {
    display: flex;
    flex-direction: column;
}

.service-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding: 14px 0;
    border-bottom: 1px dashed #eaeaea;
    transition: background-color 0.2s;
}

.service-row:hover {
    background-color: #f9f9f9;
}

.service-name {
    font-size: 16px;
    color: #333;
    line-height: 1.4;
    flex: 1;
    padding-right: 20px;
}

.service-price {
    font-size: 18px;
    font-weight: 600;
    color: #2c5f8a;
    white-space: nowrap;
}

@media (max-width: 768px) {
    .service-row {
        flex-direction: column;
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