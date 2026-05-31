<!-- resources/js/components/ServiceCard.vue -->
<template>
    <div class="service-card">
        <h3>{{ service.name }}</h3>
        <p>{{ truncateDescription(service.description) }}</p>
            <span class="service-price">от {{ formatPrice(service.price) }} руб</span>
    </div>
</template>

<script>
export default {
    name: 'ServiceCard',
    props: {
        service: {
            type: Object,
            required: true
        }
    },
    methods: {
        truncateDescription(description) {
            if (!description) return '';
            
            return description;
        },
        formatPrice(price) {
            return new Intl.NumberFormat('ru-RU').format(price);
        }
    }
}
</script>

<style scoped>
.service-card {
    border-radius: 20px;
    padding: 32px;
    display: flex;
    flex-direction: column;
    height: 100%;
    border: 2px solid #A4C3DD;
}

.service-card:hover{
    background-color: #EEF7FF;
}
h3{
    margin-bottom: 20px;
    font-size: 24px;
    font-weight: bold;
}

p{
    font-size: 19px;
    line-height: 1.6;
    margin-bottom: 20px;
}

.service-price {
    font-size: 24px;
    font-weight: 400;
    color: #2B5F8A;
    font-weight: bold;
}

@media (max-width: 768px) {
    .service-card {
        padding: 24px;
    }
    
    .service-title {
        font-size: 20px;
    }
    
    .service-description {
        font-size: 14px;
    }
    
    .service-price {
        font-size: 20px;
    }
    
    .service-button {
        padding: 8px 20px;
        font-size: 14px;
    }
}
</style>