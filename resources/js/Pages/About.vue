<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';


</script>

<template>
    <div>
        <Header />

        <div class="about-page">
            <div class="container">


                <div class="about-block">
                    <div class="about-content">
                        <h2>Добро пожаловать</h2>
                        <p>Добро пожаловать в нашу стоматологическую клинику "Happy Teeth"! Мы расположены в самом сердце города и гордимся тем, что предоставляем нашим пациентам самые современные и качественные стоматологические услуги в уютной и дружественной обстановке.</p>
                    </div>
                </div>

  
                <div class="about-block reverse">
                    <div class="about-content">
                        <h2>Наши специалисты</h2>
                        <p>В нашей клинике работает команда высококвалифицированных специалистов, которые готовы решить любые ваши стоматологические проблемы — от профилактических осмотров и чисток до сложных лечебных процедур и эстетической стоматологии. Мы используем новейшие технологии и оборудование, что позволяет нам гарантировать высокое качество лечения и безопасность наших пациентов.</p>
                    </div>
                </div>

                <div class="about-block">
                    <div class="about-content">
                        <h2>Комфорт и безопасность</h2>
                        <p>Мы понимаем, что визит к стоматологу может вызывать беспокойство, поэтому мы стараемся сделать каждое посещение максимально комфортным и безболезненным. Наши врачи уделяют внимание не только качеству лечения, но и эмоциональному состоянию пациента, предлагая индивидуальный подход и внимание к каждому.</p>
                    </div>
                </div>


                <div class="about-block reverse">
                    <div class="about-content">
                        <h2>Широкий спектр услуг</h2>
                        <p>В "Happy Teeth" мы также предлагаем широкий спектр услуг для детей и взрослых: имплантация зубов, отбеливание, ортодонтия, протезирование и многое другое. Мы стремимся к тому, чтобы каждая улыбка была здоровой и красивой! Запишитесь на консультацию уже сегодня и убедитесь, что забота о вашем здоровье — это наш главный приоритет!</p>
                    </div>
                </div>
            </div>
        </div>

        <Footer />
    </div>
</template>

<style scoped>
.about-page {
    padding-top: 50px;
    background: #f8fafc;
}

.container {
    max-width: 1300px;
    margin: 0 auto;
    padding: 0 20px;
}

.about-block {
    display: flex;
    align-items: center;
    gap: 40px;
    background: white;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    border: 2px solid #A4C3DD;
}

.about-content h2 {
    font-size: 24px;
    font-weight: 600;
    color: #265B87;
    margin-bottom: 15px;
}

.about-content p {
    font-size: 17px;
    line-height: 1.7;
}


@media (max-width: 768px) {
    .about-page {
        padding: 40px 0;
    }
    
    .about-header h1 {
        font-size: 32px;
    }
    
    .about-block {
        flex-direction: column;
        text-align: center;
        padding: 25px;
    }
    
    .about-block.reverse {
        flex-direction: column;
    }
    
    .about-number {
        margin-bottom: 15px;
    }
    
}
</style>