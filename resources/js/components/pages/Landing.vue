<template>
  <div class="landing">
    <div class="header-contain">
      <h1>Dashboard</h1>
    </div>
    <GardenMap/>

  </div>
</template>

<script>
import GardenMap from '../map/GardenMap.vue'
export default {
  name: 'Landing',
  components: {
    GardenMap
  },
  data () {
    return {
    }
  },
  mounted () {
  },
  methods: {
  }
}
</script>

<style scoped lang="scss">
.header-contain {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1em;
  h1 {
    margin: 0;
  }
}
</style>