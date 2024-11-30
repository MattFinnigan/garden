<template>
  <div class="landing">
    Landing!
  </div>
</template>

<script>
import { fetchPlants } from '../../utils/api'
export default {
  name: 'Landing',
  mounted () {
    fetchPlants().then(response => {
      console.log(response.data)
    })
  }
}
</script>

<style scoped lang="scss">

</style>