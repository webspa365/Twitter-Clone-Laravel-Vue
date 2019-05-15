<template>
  <div id="app">
    <!--img src="./assets/logo.png"-->
    <Navigation />
    <Loader />
    <router-view />
  </div>
</template>

<script>

import Navigation from '@/components/navigation/Navigation'
import Loader from '@/components/Loader'

export default {
  name: 'App',
  components: {
    Navigation,
    Loader
  }
}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 46px;
}
</style>
