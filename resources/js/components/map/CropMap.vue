<template>
  <div :class="['crop-map', { active: current.id === crop.id }, { selectable: dragging }, { dragging }]" parent-id="grid" :style="styles" @mouseup="selectCrop">
  </div>
</template>
<script>
import { updateCrop } from '../../utils/api'
import { clone, draggable, fillWithImages } from '../../utils/helpers'

export default {
  name: 'CropMap',
  props: {
    crop: {
      type: Object,
      required: true
    }
  },
  emits: ['selectCrop', 'editingCrop'],
  data () {
    return {
      loading: false,
      parent: null,
      dragging: false,
      cropCopy: clone(this.crop)
    }
  },
  mounted () {
    this.parent = this.$el?.parentElement
    if (this.crop.id) {
      draggable(this.$el, this.parent, (start) => {}, (move) => {
        if (!this.dragging) {
          this.dragging = move
        } else {
          this.cropCopy.x = move.x
          this.cropCopy.y = move.y
        }
      }, (end) => {
        this.dragging = false
        updateCrop(this, this.crop.id, { ...this.crop, x: this.cropCopy.x, y: this.cropCopy.y, month: this.month })
      })
    }
    fillWithImages(this.crop.width, this.crop.height, this.$el, { image: '/images/upload/' + this.crop.plant.image, spacing: this.crop.spacing })
  },
  watch: {
    crop: {
      handler (val) {
        this.cropCopy = clone(val)
      },
      deep: true
    }
  },
  computed: {
    styles () {
      if (!this.parent) {
        return {}
      }
      return {
        top: (this.cropCopy.y / this.parent.clientHeight) * 100 + '%',
        left: (this.cropCopy.x / this.parent.clientWidth) * 100 + '%',
        width: (this.cropCopy.width / this.parent.clientWidth) * 100 + '%',
        height: (this.cropCopy.height / this.parent.clientHeight) * 100 + '%'
      }
    },
    current () {
      return this.$store.state.crops.current || {}
    },
    month () {
      return this.$store.state.maps.month || null
    }
  },
  methods: {
    selectCrop () {
      if (!this.dragging) {
        this.$emit('editingCrop')
        this.$store.commit('crops/setCurrentCrop', this.crop)
      }
    }
  }
}
</script>

<style scoped lang="scss">
.crop-map {
  position: absolute;
  border: 1px solid $textColour;
  &:hover {
    cursor: pointer;
  }
  &.selectable {
    z-index: 1000;
    background-color: $primary2;
    border: 2px dashed $textColour;
  }
  &.dragging {
    cursor: grabbing;
  }
}
</style>