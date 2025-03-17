<template>
  <div :class="['plant', { 'selectable': dragging || selectMode }]" :title="cropTitle" :style="styles" @mouseup.prevent.self="selectCrop">
  </div>
</template>

<script>
import { clone, draggable } from '../../utils/helpers'
import { defaultCropEntry } from '../../utils/consts'
export default {
  name: 'Plant',
  emits: ['updatePositions'],
  props: {
    plant: {
      type: Object,
      required: true
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      plantCopy: clone(this.plant),
      parent: null,
      dragging: false,
      test: 0
    }
  },
  mounted () {
    this.parent = this.$el?.parentElement
    draggable(this.$el, this.parent, (start) => {
    }, (move) => {
      if (!this.dragging) {
          this.dragging = move
        } else {
          this.plantCopy.x = move.x
          this.plantCopy.y = move.y
        }
    }, (end) => {
      if (this.dragging) {
        this.checkCollision(() => {
          this.dragging = false
          this.$emit('updatePositions', this.plantCopy.entry)
        })
      }
    }, true)
  },
  watch: {
    plant: {
      handler (val) {
        this.plantCopy = clone(val)
      },
      deep: true
    }
  },
  computed: {
    cropTitle () {
      return `Crop #${this.plantCopy.entry.crop.id } ${this.plantCopy.entry.crop.plant.name} (${this.plantCopy.entry.crop.plant.variety})`
    },
    selectMode () {
      return this.$store.state.crops.selectCropMode
    },
    styles () {
      if (!this.parent) {
        return {}
      }
      return {
        // top: ((this.plantCopy.y / this.parent.clientHeight)* 100) / this.zoom + '%',
        // left: ((this.plantCopy.x / this.parent.clientWidth) * 100) / this.zoom + '%',
        top: ((this.plantCopy.y / this.parent.clientHeight)* 100) + '%',
        left: ((this.plantCopy.x / this.parent.clientWidth) * 100) + '%',
        width: this.plantCopy.entry.spacing_x + 'px',
        height: this.plantCopy.entry.spacing_y + 'px',
        // width: (this.plantCopy.l / this.parent.clientWidth) * 100 + '%',
        // height: (this.plantCopy.w / this.parent.clientHeight) * 100 + '%',
        backgroundImage: `url(./images/upload/${this.plantCopy.entry.crop.plant.image})`
      }
    }
  },
  methods: {
    checkCollision (callback) {
      const rect1 = this.$el.getBoundingClientRect()
      for (const key in this.$parent.$refs.plants) {
        const sibling = this.$parent.$refs.plants[key]
        const rect2 = sibling.$el.getBoundingClientRect()
        if (this.$el === sibling.$el) {
          continue
        }
        if (rect1.x < rect2.x + rect2.width &&
          rect1.x + rect1.width > rect2.x &&
          rect1.y < rect2.y + rect2.height &&
          rect1.y + rect1.height > rect2.y) {
          // collision detected!
          console.log('collision detected')
          // find next available position
          this.plantCopy.x = sibling.plantCopy.x
          this.plantCopy.y = sibling.plantCopy.y
          sibling.plantCopy.x = this.dragging.x
          sibling.plantCopy.y = this.dragging.y
          break
        }
      }
      callback()
    },
    selectCrop () {
      this.$store.commit('crops/setSelectCropMode', false)
      if (!this.dragging) {
        this.$store.commit('crops/setMode', 'view')
        this.$store.commit('beds/setCurrentBed', { ...this.plant.entry.bed, crop_entries: this.plant.entry.crop.crop_entries })
        this.$store.commit('crops/setCurrentCrop', this.plant.entry.crop)
        // const e = defaultCropEntry(this.plant.entry.location, this.plant.entry.bed, this.plant.entry.crop)
        // this.$store.commit('beds/setCurrentBed', { ...this.plant.entry.bed, crop_entries: this.plant.entry.crop.crop_entries })
        // this.$store.commit('crop_entries/setCurrentCropEntry', { ...this.plant.entry.crop.latest_entry })
        // this.$store.commit('crops/setCurrentCrop', this.plant.entry.crop)
      }
    }
  },
}
</script>

<style scoped lang="scss">
.plant {
  position: absolute;
  background: $primary2;
  border: 1px solid $borderColour;
  background-position: center;
  background-size: contain;
  background-repeat: no-repeat;
  min-width: 5px;
  min-height: 5px;
  &:hover {
    cursor: grab;
  }
  &.selectable {
    z-index: 100;
    border: 1px dashed;
    &.dragging {
      cursor: grabbing;
    }
  }
}

</style>