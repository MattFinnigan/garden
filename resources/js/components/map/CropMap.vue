<template>
  <div
    :class="['crop-map',
    { active }, { selectable: dragging }, { dragging }]"
    parent-id="grid"
    :style="styles"
    :title="`${crop.plant.name}&#013;${cropWidth}m x ${cropHeight}m (${crop.qty})`"
    @mouseup="selectCrop"
    @mouseenter="hovering = true"
    @mouseleave="hovering = false">
    <div v-show="hovering" class="resize-btn bottom-right" @mousedown="beginResize"></div>
  </div>
</template>
<script>
import { updateCrop } from '../../utils/api'
import { clone, debounce, draggable, fillWithImages, resizeShape } from '../../utils/helpers'

export default {
  name: 'CropMap',
  props: {
    crop: {
      type: Object,
      required: true
    }
  },
  emits: ['editingCrop'],
  data () {
    return {
      loading: false,
      parent: null,
      dragging: false,
      cropCopy: clone(this.crop),
      hovering: false,
      showDetails: false,
      resizing: false
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
    },
    hovering (val) {
      if (val) {
        debounce(() => { this.showDetails = true }, 1000)()
      } else {
        this.showDetails = false
      }
    },
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
    },
    active () {
      return this.crop.id === this.current.id
    },
    cropWidth () {
      return (this.crop.width / 100).toFixed(1)
    },
    cropHeight () {
      return (this.crop.height / 100).toFixed(1)
    }
  },
  methods: {
    beginResize () {
      this.resizing = true
      resizeShape(this.parent, this.$el, this.crop.x, this.crop.y, (shape, width, height) => {
        // moving
        if (this.resizing) {
          this.cropCopy.width = width
          this.cropCopy.height = height
        }
      }, (shapeRect) => {
        // end
        this.resizing = false
        updateCrop(this, this.crop.id, { ...this.crop, width: shapeRect.width, height: shapeRect.height, month: this.month })
     }, { image: '/images/upload/' + this.crop.plant.image, spacing: this.crop.spacing })
    },
    selectCrop () {
      if (!this.dragging && !this.resizing) {
        if (this.crop.id === this.current.id) {
          this.$store.commit('crops/setMode', 'edit')
        } else {
          this.$store.commit('crops/setCurrentCrop', this.crop)
        }
      }
    }
  }
}
</script>

<style scoped lang="scss">
.crop-map {
  position: absolute;
  border: 1px solid $textColour;
  > :not(.resize-btn) {
    pointer-events: none;
    user-select: none;
  }
  &:hover {
    cursor: pointer;
  }
  &.selectable {
    z-index: 10;
    background-color: $primary2;
    border: 2px dashed $textColour;
  }
  &.active {
    background-color: $primary3;
  }
  &.dragging {
    cursor: grabbing;
  }
  :deep(.shapeWidth),
  :deep(.shapeHeight),
  :deep(.shapeQty) {
    position: absolute;
    color: $textColour;
    padding: 0.25em 0.5em;
    font-size: $fsSmall;
  }
  :deep(.shapeWidth) {
    top: -25px;
    left: 50%;
    transform: translateX(-50%);
  }
  :deep(.shapeHeight) {
    left: -45px;
    top: 50%;
    transform: translateY(-50%);
  }
  :deep(.shapeQty) {
    right: -50px;
    top: 50%;
    transform: translateY(-50%);
  }
  .resize-btn {
    position: absolute;
    width: 10px;
    height: 10px;
    background-color: white;
    border: 1px solid $textColour;
    z-index: 1001;
    &.top-left {
      top: -5px;
      left: -5px;
      cursor: nwse-resize;
    }
    &.top-right {
      top: -5px;
      right: -5px;
      cursor: nesw-resize;
    }
    &.bottom-left {
      bottom: -5px;
      left: -5px;
      cursor: nesw-resize;
    }
    &.bottom-right {
      bottom: -5px;
      right: -5px;
      cursor: nwse-resize;
    }
  }
  &:hover::after {
    display: block;
    position: absolute;
    content: attr(data-tooltip);
    background-color: white;
    border: 1px solid $textColour;
    padding: 5px;
    font-size: $fsSmall;
  }
}
</style>