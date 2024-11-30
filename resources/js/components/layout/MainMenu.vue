
<template>
  <div class="main-menu">
    <nav>
      <ul>
        <li><router-link to="/">Home</router-link></li>
        <li><Button styling="link" @click="newPlant = true">New plant</Button></li>
        <li><Button styling="link" @click="newPlot = true">New plot</Button></li>
        <li><router-link to="/crops">Crops</router-link></li>
      </ul>
    </nav>
    <PlantForm v-if="newPlant" @close="newPlant = false"/>
    <PlotForm v-if="newPlot" @close="newPlot = false"/>
  </div>
</template>

<script>
import PlantForm from '../forms/PlantForm.vue';
import PlotForm from '../forms/PlotForm.vue';

export default {
  components: {
    PlantForm,
    PlotForm
  },
  data () {
    return {
      newPlant: false,
      newPlot: false
    }
  }
}
</script>
<style lang="scss" scoped>
nav {
  ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex;
    li {
      margin-right: 1rem;
      a {
        text-decoration: none;
        color: blue;
        &:hover {
          text-decoration: underline;
        }
      }
    }
  }
}
</style>
