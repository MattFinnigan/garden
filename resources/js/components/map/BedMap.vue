<template>
  <div :id="'bedmap' + bed?.id || 'new'" :class="['bed-map', { active: current.id === bed.id }, { selectable: dragging || selectionMode }, { dragging }]" parent-id="grid" :style="styles" @mouseup.prevent.self="selectBed">
    <Plant v-for="plant in plants" ref="plants" :plant="plant" :loading="loading" @updatePositions="updatePositions"/>
  </div>
</template>
<script>
import Plant from './Plant.vue'
import { updateCropEntry, updateBed } from '../../utils/api'
import { clone, draggable } from '../../utils/helpers';

export default {
  name: 'BedMap',
  components: {
    Plant
  },
  props: {
    bed: {
      type: Object,
      required: true
    },
    selectionMode: {
      type: Boolean,
      default: false
    }
  },
  emits: ['selectBed', 'editingBed'],
  data () {
    return {
      loading: false,
      parent: null,
      dragging: false,
      bedCopy: clone(this.bed)
    }
  },
  mounted () {
    this.parent = this.$el?.parentElement
    if (this.bed.id) {
      draggable(this.$el, this.parent, (start) => {}, (move) => {
        if (this.selectionMode) {
          return
        }
        if (!this.dragging) {
          this.dragging = move
        } else {
          this.bedCopy.x = move.x
          this.bedCopy.y = move.y
        }
      }, (end) => {
        this.dragging = false
        updateBed(this, this.bed.id, { ...this.bed, x: this.bedCopy.x, y: this.bedCopy.y })
      })
    }
  },
  watch: {
    bed: {
      handler (val) {
        this.bedCopy = clone(val)
      },
      deep: true
    }
  },
  computed: {
    plants () {
      const res = []
      // this.bed.crop_entries.forEach(ce => {
      //   if (JSON.parse(ce.plant_pos)) {
      //     JSON.parse(ce.plant_pos).forEach(plant => {
      //       res.push({ ...plant, entry: ce })
      //     })
      //   }
      // })
      return res
    },
    styles () {
      if (!this.parent) {
        return {}
      }
      return {
        top: (this.bedCopy.y / this.parent.clientHeight) * 100 + '%',
        left: (this.bedCopy.x / this.parent.clientWidth) * 100 + '%',
        width: (this.bedCopy.width / this.parent.clientWidth) * 100 + '%',
        height: (this.bedCopy.height / this.parent.clientHeight) * 100 + '%'
      }
    },
    current () {
      return this.$store.state.beds.current || {}
    }
  },
  methods: {
    selectBed () {
      if (!this.dragging) {
        this.$emit('editingBed')
        this.$store.commit('beds/setCurrentBed', this.bed)
      }
      if (this.selectionMode) {
        this.$emit('editingBed')
        this.$emit('selectBed', this.bed)
      }
    },
    updatePositions (entry) {
      this.loading = true
      const data = []
      this.$refs.plants.forEach(plant => {
        if (plant.plantCopy.entry.id === entry.id) {
          data.push({
            x: plant.plantCopy.x,
            y: plant.plantCopy.y,
          })
        }
      })
      this.loading = false
      updateCropEntry(this, entry.id, { ...entry, plant_pos: JSON.stringify(data) }, false).then(response => {
        this.loading = false
      })
    }
  },
}
</script>

<style scoped lang="scss">
.bed-map {
  position: absolute;
  background: $secondary2;
  &:hover {
    cursor: pointer;
  }
  &.selectable {
    z-index: 1000;
    background-color: $primary3;
    border: 2px dashed $textColour;
  }
  &.dragging {
    cursor: grabbing;
  }
}
</style>