<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import ContactForm from '@/Components/ContactForm.vue';
</script>

<template>
  <div>
    <h1>Моя страница</h1>

    <Header />
    <div img-cont>
<img src="/assets/images/compressio_image.jpeg" alt=""></div>
      <ContactForm />
    <Footer />
  </div>
</template>

<style scoped>
img{
border: 3px solid #265B87;
border-radius: 30px;
margin-top: 35px;
margin-bottom: 25px;
display: block;
margin-left: auto;
margin-right: auto;
}


</style>