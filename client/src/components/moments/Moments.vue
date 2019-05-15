<template>
  <div class='moments'>
    <h1>Moments</h1>
  </div>
</template>

<script>
require('./Moments.css');
import {http} from '@/modules/http';
import {server} from '@/modules/server';

export default {
  name: 'moments',
  data: function () {
    return {
      msg: ''
    }
  },
  methods: {
    post_data: function() {
    }
  },
  mounted() {

  },
  beforeDestroy() {

  }
}
</script>
