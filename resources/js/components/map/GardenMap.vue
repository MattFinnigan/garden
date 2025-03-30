<template>
  <div class="garden-map">
    <!-- <div class="controls-row">
      <div class="date-select">
        <Button class="icon secondary" @click="removeDays(7)"><Icon name="rewind" size="16px" maskSize="15px"></Icon></Button>
        <Button class="icon secondary" @click="removeDays(1)"><Icon name="play reverse"></Icon></Button>
        <Button class="icon secondary" @click="addDays(1)"><Icon name="play"></Icon></Button>
        <Button class="icon secondary" @click="addDays(7)"><Icon name="rewind reverse" size="16px" maskSize="15px"></Icon></Button>
      </div>
        <Button class="primary outline icon" @click="zoomToFull"><Icon name="zoomin" colour="primary" maskSize="15px" size="14px"></Icon></Button> 
        <Button class="primary outline icon" @click="zoom += 0.1"><Icon name="zoomin" colour="primary" maskSize="15px" size="14px"></Icon></Button>
        <Button class="primary outline icon" @click="zoom -= 0.1"><Icon name="zoomout" colour="primary" maskSize="15px" size="14px"></Icon></Button>
    </div> -->
    <div id="grid" class="grid" ref="grid" :style="styles" @click.prevent.self="() => { cancelBed(); cancelCropEntry(); cancelCropSelect() }">
      <div v-if="newBedExplain" class="new-bed-explain" @mousedown="beginNewBed">
        <h4>Click and drag to create a new bed</h4>
      </div>
      <BedMap v-for="bed in beds" :key="'bed' + bed.id" :zoom="zoom" :bed="bed" ref="bed" :selectionMode="currentCropEntry && !currentBed" @editingBed="editingBed = true"/>
    </div>
    <!-- <Crops v-else-if="!loading" :embedded="true"></Crops> -->
    <Modal v-if="currentBed && editingBed" @close="cancelBed()">
      <template #header>
        <h5>{{ bedFormText.heading }}</h5>
        <p>{{ bedFormText.text }}</p>
      </template>
      <template #content>
        <BedsForm @done="bedSubmitted()">
          <template #buttons>
            <Button type="submit" classes="secondary2" @click="bedSubmitted = bedUpdated">Done</Button>
            <Button class="primary" type="submit" @click="bedSubmitted = createNewCrop">Add a Crop</Button>
          </template>
        </BedsForm>
      </template>
    </Modal>
    <Modal v-if="currentCropEntry && currentBed && mode === 'edit'" v-show="!currentPlant" @close="cancelCropEntry">
      <template #header>
        <h5>Create a new Crop {{ currentCropEntry.crop_id ? 'Entry' : '' }}</h5>
        <p v-if="!currentCropEntry.crop_id">You've selected a bed. Now let's add a Crop with it's first Entry</p>
        <p v-else>Add a new Entry to this Crop</p>
      </template>
      <template #content>
        <CropEntryForm @done="cropEntrySubmitted()" @remove="removeCropEntry" @createPlant="">
          <template #buttons>
            <Button v-if="editingBed" type="submit" classes="secondary2" @click="cropEntrySubmitted = createNewCrop">Submit & create another</Button>
            <Button class="primary" type="submit" @click="cropEntrySubmitted = cancelCropEntry">Done</Button>
          </template>
        </CropEntryForm>
      </template>
    </Modal>
    <Modal v-if="currentCrop && mode !== 'edit'" @close="cancelCropEntry(true)">
      <template #header>
        <h5>Crop #{{ currentCrop.id }} - {{ currentCrop.plant.name }}</h5>
        <p>Select & view a crop entry for more details.</p>
      </template>
      <template #content>
        <Crop :embedded="true" @new="createNewCrop(false)" @close="cancelCropEntry(true)"></Crop>
      </template>
    </Modal>
  </div>
</template>

<script>
import { fetchCrops, fetchBeds, fetchPlants, deleteCropEntry, updateCropEntry } from '../../utils/api'
import { watchScreenSize } from '../../utils/helpers'
import { defaultCrop, defaultCropEntry, defaultBed } from '../../utils/consts'

import BedMap from './BedMap.vue'
import BedsForm from '../forms/BedsForm.vue'
import Modal from '../common/Modal.vue'
import CropEntryForm from '../forms/CropEntryForm.vue'
import PlantForm from '../forms/PlantForm.vue'
import Crops from '../pages/Crops.vue'

import Crop from '../pages/Crop.vue'

export default {
  name: 'GardenMap',
  components: {
    BedMap,
    BedsForm,
    Modal,
    CropEntryForm,
    PlantForm,
    Crop,
    Crops
  },
  data () {
    return {
      loading: true,
      newBedExplain: false,
      showNewMenu: false,
      zoom: 1,
      bedSubmitted: null,
      cropEntrySubmitted: null,
      render: true,
      zoom: 1,
      editingBed: false
    }
  },
  mounted () {
    this.fetchCrops()
    fetchBeds(this)
    watchScreenSize(() => {
      this.render = false
      this.$nextTick(() => {
        this.render = true
        this.$nextTick(() => {
          this.zoomToFull()
        })
      })
    })
  },
  computed: {
    month: {
      get () {
        return this.$store.state.maps.month
      },
      set (value) {
        return this.$store.commit('maps/setMapMonth', value)
      }
    },
    maps () {
      return this.$store.state.maps.list
    },
    styles () {
      return {
        height: 'calc(100vh - 60px)',
        width: '100%'
      }
    },
    plants () {
      return this.$store.state.plants.list
    },
    currentPlant () {
      return this.$store.state.plants.current
    },
    beds () {
      return this.$store.state.beds.list
    },
    currentBed () {
      return this.$store.state.beds.current
    },
    bedFormText () {
      return {
        heading: this.currentBed.id ? 'Update Bed' : 'New Bed',
        text: this.currentBed.id ? 'Here you can change the Bed\'s details or you can remove it if there aren\'t any active crops.' : 'You’ve placed your new bed. Now just give it a name, & maybe a happy snap'
      }
    },
    currentCropEntry () {
      return this.$store.state.crop_entries.current
    },
    currentCrop () {
      return this.$store.state.crops.current
    },
    mode () {
      return this.$store.state.crops.mode
    }
  },
  methods: {
    plantSubmitted () {
    },
    cancelPlant () {
      this.$store.commit('plants/setCurrentPlant', null)
    },
    selectCropMode () {
      this.$store.commit('crops/setSelectCropMode', !this.$store.state.crops.selectCropMode)
      this.showNewMenu = false
    },
    cancelCropSelect () {
      this.$store.commit('crops/setSelectCropMode', false)
      this.showNewMenu = false
    },
    removeCropEntry () {
      deleteCropEntry(this, this.currentCropEntry.id)
    },
    cancelCropEntry (close = false) {
      if (this.editingBed || close) {
        this.$store.commit('crops/setMode', 'edit')
        this.cropEntrySubmitted = null
        this.$store.commit('crops/setCurrentCrop', null)
        this.cancelBed()
      } else {
        this.$store.commit('crops/setMode', 'view')
      }
      this.$store.commit('crop_entries/setCurrentCropEntry', null)
    },
    bedUpdated () {
      this.cancelBed()
      this.bedSubmitted = null
    },
    zoomToFull () {
      if (this.$el.offsetWidth > this.$refs.grid?.offsetWidth) {
        this.$refs.grid.style.zoom = (this.$el.offsetWidth / this.$refs.grid?.offsetWidth)
        this.zoom = (this.$el.offsetWidth / this.$refs.grid?.offsetWidth)
      }
    },
    createNewCrop (editingBed = true) {
      this.editingBed = editingBed
      this.$store.commit('crops/setMode', 'edit')
      this.$store.commit('crops/setSelectCropMode', false)
      this.$nextTick(() => {
        this.bedSubmitted = null
        this.cropEntrySubmitted = null
        this.showNewMenu = false
        const promises = []
        if (!this.plants.length) {
          promises.push(fetchPlants(this))
        }
        Promise.all(promises).then(() => {
          const e = defaultCropEntry(this.currentBed, this.currentCrop)
          const c = defaultCrop(this.plants[0])
          if (!this.currentCrop) {
            this.$store.commit('crops/setCurrentCrop', c)
            this.$store.commit('crop_entries/setCurrentCropEntry', e)
          } else {
            this.$store.commit('crop_entries/setCurrentCropEntry', { ...this.currentCrop.latest_entry, id: null, datetimestamp: new Date().toISOString().slice(0, 16) })
          }
        })
      })
    },
    beginNewBed (e) {
      this.newBedExplain = false
      const ev = new MouseEvent('mousedown', {
        bubbles: false,
        cancelable: true,
        view: window,
        clientX: e.clientX,
        clientY: e.clientY
      })
      this.$refs.grid.dispatchEvent(ev)
    },
    cancelBed ()  {
      this.editingBed = false
      if (this.$refs.grid.querySelector('.newBed')) {
        this.$refs.grid.removeChild(this.$refs.grid.querySelector('.newBed'))
        this.newBedExplain = false
      }
      this.$store.commit('beds/setCurrentBed', null)
    },
    createNewBed () {
      this.editingBed = true
      this.$store.commit('crops/setSelectCropMode', false)
      this.newBedExplain = true
      this.showNewMenu = false

      const newBed = defaultBed()

      let isDragging = false
      let startX = 0
      let startY = 0
      let shape = null
      const parent = this.$refs.grid

      const getZoomFactor = () => {
        const computedStyle = window.getComputedStyle(parent)
        return parseFloat(computedStyle.zoom) || 1
      }

      const onMouseMove = (e) => {
        if (!isDragging || !shape) {
          return
        }

        const parentRect = parent.getBoundingClientRect()
        const zoomFactor = getZoomFactor()

        // Calculate the current mouse position relative to the parent
        const currentX = Math.min(
          parentRect.width,
          Math.max(0, (e.clientX - parentRect.left) / zoomFactor)
        )
        const currentY = Math.min(
          parentRect.height,
          Math.max(0, (e.clientY - parentRect.top) / zoomFactor)
        )

        // Calculate width and height of the shape
        const width = Math.abs(currentX - startX)
        const height = Math.abs(currentY - startY)

        // Update shape dimensions and position
        shape.style.left = `${Math.min(startX, currentX)}px`
        shape.style.top = `${Math.min(startY, currentY)}px`
        shape.style.width = `${width}px`
        shape.style.height = `${height}px`
        shape.querySelector('.shapeWidth').innerText = `${(width / 100).toFixed(1)}m`
        shape.querySelector('.shapeHeight').innerText = `${(height / 100).toFixed(1)}m`
      }

      const onMouseUp = () => {
        if (!isDragging || !shape) {
          return
        }

        isDragging = false

        document.removeEventListener('mousemove', onMouseMove)
        document.removeEventListener('mouseup', onMouseUp)
        parent.removeEventListener('mousedown', onMouseDown)

        const shapeRect = shape.getBoundingClientRect()
        const parentRect = parent.getBoundingClientRect()
        const zoomFactor = getZoomFactor()

        // Update the new bed's properties
        newBed.x = (shapeRect.left - parentRect.left) / zoomFactor
        newBed.y = (shapeRect.top - parentRect.top) / zoomFactor
        newBed.width = shapeRect.width / zoomFactor
        newBed.height = shapeRect.height / zoomFactor
        if (newBed.width > 10 && newBed.height > 10) {
          this.$store.commit('beds/setCurrentBed', newBed)
        } else {
          this.cancelBed()
        }
        shape = null
      }

      const onMouseDown = (e) => {
        if (e.target !== parent) {
          return
        }

        this.newBedExplain = false
        const parentRect = parent.getBoundingClientRect()
        const zoomFactor = getZoomFactor()

        startX = Math.min(
          parentRect.width,
          Math.max(0, (e.clientX - parentRect.left) / zoomFactor)
        )
        startY = Math.min(
          parentRect.height,
          Math.max(0, (e.clientY - parentRect.top) / zoomFactor)
        )

        // Create the shape element
        shape = document.createElement('div')
        shape.className = 'newBed'
        shape.style.left = `${startX}px`
        shape.style.top = `${startY}px`
        shape.style.width = '0px'
        shape.style.height = '0px'

        const shapeWidth = document.createElement('div')
        shapeWidth.className = 'shapeWidth'
        shapeWidth.innerText = '0cm'
        
        const shapeHeight = document.createElement('div')
        shapeHeight.className = 'shapeHeight'
        shapeHeight.innerText = '0cm'

        if (startX < 40) {
          shapeHeight.style.left = '0'
        }
        if (startY < 40) {
          shapeWidth.style.top = '0'
        }

        parent.appendChild(shape)
        shape.appendChild(shapeWidth)
        shape.appendChild(shapeHeight)
        isDragging = true

        document.addEventListener('mousemove', onMouseMove)
        document.addEventListener('mouseup', onMouseUp)
      }

      parent.addEventListener('mousedown', onMouseDown)
    },
    fetchCrops () {
      this.loading = true
      if (!this.month) {
        this.month = new Date().getMonth() + 1
      }
      fetchCrops(this, this.month).then(response => {
        this.loading = false
        this.$nextTick(() => {
          this.zoomToFull()
          // this.$nextTick(() => {
          //   this.location.beds.forEach((bed, i) => {
          //     arrangePlantsInBedWithOverlapCheck(bed, (entries) => {
          //       entries.forEach(entry => {
          //         updateCropEntry(this, entry.id, entry, false)
          //       })
          //     })
          //   })
          // })
        })
      })
    },
    addDays (days) {
      const date = new Date(this.date)
      date.setDate(date.getDate() + days + 1)
      this.date = date
    },
    removeDays (days) {
      const date = new Date(this.date)
      date.setDate(date.getDate() - days)
      this.date = date
    }
  }
}
</script>

<style scoped lang="scss">
.grid {
  position: relative;
  background: $primary2;
  max-height: 80vh;
  max-width: 100%;
  overflow: auto;
  zoom: 1;
    background-image:
      repeating-linear-gradient(#ccc 0 1px, transparent 1px 100%),
      repeating-linear-gradient(90deg, #ccc 0 1px, transparent 1px 100%);
  :deep(.newBed) {
    border: 2px dashed $textColour;
    background-color: $primary3;
    position: absolute;
    border-radius: 0.5em;
    .shapeWidth,
    .shapeHeight {
      position: absolute;
      color: $textColour;
      padding: 0.25em 0.5em;
      font-size: $fsSmall;
    }
    .shapeWidth {
      top: -25px;
      left: 50%;
      transform: translateX(-50%);
    }
    .shapeHeight {
      left: -45px;
      top: 50%;
      transform: translateY(-50%);
    }
  }
  .new-bed-explain {
    width: 100%;
    text-align: center;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: $primary3;
    border-radius: 0.5em;
    border: 2px dashed $textColour;
    z-index: 999;
    h4 {
      color: $backgroundColour;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
  }
}
</style>