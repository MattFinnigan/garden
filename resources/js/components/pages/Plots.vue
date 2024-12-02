<template>
  <div class="plot-page">
    <div class="header-contain">
      <h1>Plots</h1>
      <Button classes="sm" @click="currPlot = { name: '', beds: [] }">Add Plot</Button>
    </div>
    <div v-if="loading">Loading...</div>
    <div v-else>
      <div v-for="plot in plots" :key="plot.id">
        <Card :title="plot.name" :description="plot.description" :image="plot.image">
          <template #actions>
            <Button classes="sm" @click="editPlot(plot)">Edit</Button>
            <Button classes="sm" @click="handleDelete(plot.id)">Delete</Button>
          </template>
          <template #content>
            <Table :headers="[{ label: 'Name', key: 'name' }, { label: 'Description', key: 'description' }]" :rows="plot.beds">
              <template #header>
                <h3>Beds</h3>
              </template>
            </Table>
          </template>
        </Card>
      </div>
    </div>
    <PlotForm
      v-if="currPlot"
      :val="currPlot"
      @add="p => plots.push(p)"
      @patch="p => {
        const index = plots.findIndex(plot => plot.id === p.id)
        plots.splice(index, 1, p)
      }"
      @close="currPlot = null"/>
  </div>
</template>

<script>
import Card from '../common/Card.vue'
import PlotForm from '../forms/PlotForm.vue';
import { fetchPlots, deletePlot } from '../../utils/api'
import { clone } from '../../utils/helpers'
import BedInput from '../forms/BedInput.vue'

export default {
  name: 'Plots',
  components: {
    Card,
    PlotForm,
    BedInput
  },
  data () {
    return {
      loading: true,
      plots: [],
      currPlot: null
    }
  },
  mounted () {
    this.getPlots()
  },
  methods: {
    getPlots () {
      fetchPlots().then(response => {
        this.plots = response.data
      }).finally(() => {
        this.loading = false
      })
    },
    handleDelete (id) {
      this.loading = true
      deletePlot(id).then(response => {
        this.plots = this.plots.filter(plot => plot.id !== id)
      }).finally(() => {
        this.loading = false
      })
    },
    editPlot (plot) {
      this.currPlot = clone(plot)
    }
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