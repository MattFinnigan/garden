<template>
  <div class="plot-form">
  <Modal @close="$emit('close')">
    <template #header>
      <h2>Plot Form</h2>
    </template>
    <template #content>
      <Form @submit="submitForm">
        <template #inputs>
          <Input type="text" v-model="currPlot.name" label="Plot name" required minlength="2" maxlength="255"/>
          <Table
            :headers="[{ label: 'Name', key: 'name' }, { label: 'Description', key: 'description' }]"
            :rows="currPlot.beds"
            :actions="{ edit: true, delete: true }"
            @edit="(bed, i) => currBed = { ...clone(bed), index: i }"
            @delete="deleteBed">
            <template #header>
              <h3>Beds</h3>
              <Button classes="sm" @click="currBed = null">Add</Button>
            </template>
            <template #prefix>
              <div v-if="currBed !== false" class="bed-form">
                <BedInput :exisiting="currBed" @done="addBed"/>
              </div>
            </template>
          </Table>
        </template>
        <template #buttons>
          <Button type="submit" :disabled="loading">Submit</Button>
        </template>
      </Form>
    </template>
  </Modal>
</div>
</template>

<script>
import { createPlot, updatePlot } from '../../utils/api'
import { isEmpty, clone } from '../../utils/helpers';
import BedInput from '../forms/BedInput.vue'

export default {
  components: {
    BedInput
  },
  name: 'PlotForm',
  props: {
    val: Object,
  },
  emits: ['add', 'patch', 'close'],
  data () {
    return {
      clone,
      currPlot: !isEmpty(this.val) ? this.val : { name: '', beds: [] },
      loading: false,
      currBed: false
    }
  },
  methods: {
    addBed (bed) {
      if (bed.id) {
        this.currPlot.beds = this.currPlot.beds.map(b => {
          if (b.id === bed.id) {
            return bed
          }
          return b
        })
      } else if (bed.index !== undefined) {
        this.currPlot.beds[bed.index] = bed
      } else {
        this.currPlot.beds.push(bed)
      }
      this.currBed = false
    },
    deleteBed (bed) {
      this.currPlot.beds = this.currPlot.beds.filter(b => b !== bed)
    },
    submitForm (e) {
      this.loading = true
      e.preventDefault()
      if (this.currPlot.id) {
        updatePlot(this.currPlot.id, this.currPlot).then(response => {
          this.$emit('patch', response.data.data)
        }).finally(() => {
          this.loading = false
          this.$emit('close')
        })
      } else {
        createPlot(this.currPlot).then(response => {
          this.$emit('add', response.data.data)
        }).finally(() => {
          this.loading = false
          this.$emit('close')
        })
      }
    }
  },
  computed: {
    // Add your computed properties here
  },
  mounted() {
    // Add your mounted logic here
  }
}
</script>

<style scoped lang="scss">
h2 {
  margin: 0;
}
.bed-form {
  background: $grey-800;
  padding: 1em;
  margin-bottom: 1.5em;
  border-radius: 0.5em;
}
</style>