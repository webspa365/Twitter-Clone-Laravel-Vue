<template>
  <div class='home'>
    <h1>Home</h1>
  </div>
</template>

<script>
require('./Home.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

export default {
  name: 'Home',
  data: function () {
    return {
      msg: ''
    }
  },
  methods: {
    post_data: function(e) {
      var comp = this;
    }
  },
  mounted() {

  },
  beforeDestroy() {

  }
}
</script>
