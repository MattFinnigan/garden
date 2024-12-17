<template>
  <div class="garden-map">
    <div class="controls-row">
      <div class="date-select">
        <Button class="icon secondary" @click="removeDays(7)"><Icon name="rewind" size="16px" maskSize="15px"></Icon></Button>
        <Button class="icon secondary" @click="removeDays(1)"><Icon name="play reverse"></Icon></Button>
        <Input type="date" v-model="date" @change="fetchMaps"/>
        <Button class="icon secondary" @click="addDays(1)"><Icon name="play"></Icon></Button>
        <Button class="icon secondary" @click="addDays(7)"><Icon name="rewind reverse" size="16px" maskSize="15px"></Icon></Button>
      </div>
      <div class="buttons-contain">
        <Select v-model.number="location_id" :options="maps.map(l => { return { label: l.name, value: l.id } })" label="Location"/>
        <!-- <Button class="primary outline icon" @click="zoomToFull"><Icon name="zoomin" colour="primary" maskSize="15px" size="14px"></Icon></Button> -->
        <!-- <Button class="primary outline icon" @click="zoom += 0.1"><Icon name="zoomin" colour="primary" maskSize="15px" size="14px"></Icon></Button>
        <Button class="primary outline icon" @click="zoom -= 0.1"><Icon name="zoomout" colour="primary" maskSize="15px" size="14px"></Icon></Button> -->
        <Button class="primary icon" @click="showNewMenu = !showNewMenu"><Icon name="plus"></Icon></Button>
        <div :class="['dropdown', { 'show': showNewMenu }]">
          <div class="item">Location</div>
          <div class="item" @click="createNewBed">Bed</div>
          <div class="item" @click="createNewCrop">Crop</div>
          <div class="item" @click="selectCropMode">Crop Entry</div>
        </div>
      </div>
    </div>
    <div id="grid" class="grid" ref="grid" :style="styles" @click.prevent.self="() => { cancelBed(); cancelCropEntry(); cancelCropSelect() }">
      <div v-if="newBedExplain" class="new-bed-explain" @mousedown="beginNewBed">
        <h4>Click and drag to create a new bed</h4>
      </div>
      <BedMap v-for="bed in locationBeds" :key="'bed' + bed.id" :zoom="zoom" :bed="bed" ref="bed" :selectionMode="currentCropEntry && !currentBed" @editingBed="editingBed = true" @positionUpdated="fetchMaps"/>
    </div>
    <Modal v-if="currentBed && editingBed" @close="cancelBed(currentBed)">
      <template #header>
        <h5>{{ bedFormText.heading }}</h5>
        <p>{{ bedFormText.text }}</p>
      </template>
      <template #content>
        <BedsForm @done="bedSubmitted()" @remove="(bed) => { removeBed(bed, true) }">
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
        <CropEntryForm @done="cropEntrySubmitted()" @remove="removeCropEntry" @createPlant="createNewPlant">
          <template #buttons>
            <Button v-if="editingBed" type="submit" classes="secondary2" @click="cropEntrySubmitted = createNewCrop">Submit & create another</Button>
            <Button class="primary" type="submit" @click="cropEntrySubmitted = cancelCropEntry">Done</Button>
          </template>
        </CropEntryForm>
      </template>
    </Modal>
    <Modal v-if="currentPlant && mode === 'edit'" @close="cancelPlant">
      <template #header>
        <h5>Create a new Plant </h5>
        <p>You've selected a bed. Now let's add a Plant with it's first Entry</p>
      </template>
      <template #content>
        <PlantForm @done="plantSubmitted()">
          <template #buttons>
            <Button class="primary" type="submit" >Done</Button>
          </template>
        </PlantForm>
      </template>
    </Modal>
    <Modal v-if="currentCrop && mode !== 'edit'" @close="cancelCropEntry(true)">
      <template #header>
        <h5>Crop #{{ currentCrop.id }} - {{ currentCrop.plant.name }}</h5>
        <p>Select & view a crop entry for more details.</p>
        <Display label="Location" :val="location.name + ' (' + currentBed.name + ')'"></Display>
      </template>
      <template #content>
        <Crop :embedded="true" @new="createNewCrop(false)" @close="cancelCropEntry(true)"></Crop>
      </template>
    </Modal>
  </div>
</template>

<script>
import { fetchMaps, updateBed, fetchPlants, deleteCropEntry, updateCropEntry } from '../../utils/api'
import { watchScreenSize, arrangePlantsInBedWithOverlapCheck } from '../../utils/helpers'
import { defaultCrop, defaultCropEntry } from '../../utils/consts'

import BedMap from './BedMap.vue'
import BedsForm from '../forms/BedsForm.vue'
import Modal from '../common/Modal.vue'
import CropEntryForm from '../forms/CropEntryForm.vue'
import PlantForm from '../forms/PlantForm.vue'

import Crop from '../pages/Crop.vue'

export default {
  name: 'GardenMap',
  components: {
    BedMap,
    BedsForm,
    Modal,
    CropEntryForm,
    PlantForm,
    Crop
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
    this.date = new Date()
    this.fetchMaps()
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
    date: {
      get () {
        return this.$store.state.maps.date?.split('T')[0]
      },
      set (value) {
        return this.$store.commit('maps/setMapDate', value.toISOString())
      }
    },
    maps () {
      return this.$store.state.maps.list
    },
    styles () {
      return {
        height: `${this.location?.w}px`,
        width: `${this.location?.l}px`,
        backgroundSize: `${2 * this.zoom}% 10px`
      }
    },
    location () {
      return this.$store.state.locations.current
    },
    locations () {
      return this.$store.state.locations.list
    },
    locationBeds () {
      return this.maps.find(l => l.id === this.location_id)?.beds || []
    },
    location_id: {
      get () {
        return this.$store.state.locations.current?.id || null
      },
      set (value) {
        this.$store.commit('locations/setCurrentLocation', this.maps.find(l => l.id === value))
      }
    },
    plants () {
      return this.$store.state.plants.list
    },
    currentPlant () {
      return this.$store.state.plants.current
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
  watch: {
    location_id (value) {
      this.zoomToFull()
    }
  },
  methods: {
    createNewPlant () {
      this.$store.commit('plants/setCurrentPlant', {
        name: '',
        variety: '',
        description: '',
        days_to_harvest: 1,
        image: ''
      })
      this.$nextTick(() => {
        this.fetchMaps()
      })
    },
    plantSubmitted () {
      this.fetchMaps()
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
      deleteCropEntry(this, this.currentCropEntry.id).then(() => {
        this.fetchMaps()
      })
    },
    cancelCropEntry (close = false) {
      if (this.editingBed || close) {
        this.$store.commit('crops/setMode', 'edit')
        this.cropEntrySubmitted = null
        this.$store.commit('crops/setCurrentCrop', null)
        this.cancelBed(this.currentBed)
      } else {
        this.$store.commit('crops/setMode', 'view')
      }
      this.$store.commit('crop_entries/setCurrentCropEntry', null)
      this.fetchMaps()
    },
    bedUpdated () {
      this.cancelBed(this.currentBed)
      this.fetchMaps()
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
        this.fetchMaps()
        this.bedSubmitted = null
        this.cropEntrySubmitted = null
        this.showNewMenu = false
        const promises = []
        if (!this.plants.length) {
          promises.push(fetchPlants(this))
        }
        Promise.all(promises).then(() => {
          const e = defaultCropEntry({ id: this.location_id }, this.currentBed, this.currentCrop)
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
    removeBed (bed) {
      updateBed(this, bed.id, { ...bed, deactivated: this.date }, false).then(() => {
        this.$store.commit('beds/setCurrentBed', null)
        this.fetchMaps()
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
    cancelBed (bed)  {
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

      const newBed = {
        x: 0,
        y: 0,
        l: 0,
        h: 0,
        name: '',
        description: '',
        images: [],
        crop_entries: []
      }
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
        shape.querySelector('.shapeWidth').innerText = `${width.toFixed(0)}cm`
        shape.querySelector('.shapeHeight').innerText = `${height.toFixed(0)}cm`
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
        newBed.l = shapeRect.width / zoomFactor
        newBed.w = shapeRect.height / zoomFactor

        // Check for collisions with existing beds
        const beds = this.$refs.bed || []
        const hasCollision = beds.some(bed => {
          const bedRect = bed.$el.getBoundingClientRect()
          const bedLeft = (bedRect.left - parentRect.left) / zoomFactor
          const bedTop = (bedRect.top - parentRect.top) / zoomFactor
          const bedRight = bedLeft + (bedRect.width / zoomFactor)
          const bedBottom = bedTop + (bedRect.height / zoomFactor)

          const newBedLeft = newBed.x
          const newBedTop = newBed.y
          const newBedRight = newBed.x + newBed.l
          const newBedBottom = newBed.y + newBed.w

          return !(
            newBedRight <= bedLeft || // No overlap on the left
            newBedLeft >= bedRight || // No overlap on the right
            newBedBottom <= bedTop || // No overlap on the top
            newBedTop >= bedBottom    // No overlap on the bottom
          )
        })

        if (hasCollision) {
          this.cancelBed()
        } else {
          this.$store.commit('beds/setCurrentBed', newBed)
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
    fetchMaps () {
      this.loading = true
      fetchMaps(this, this.date).then(response => {
        this.loading = false
        this.$store.commit('locations/setCurrentLocation', response.data[0])
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
      this.fetchMaps()
    },
    removeDays (days) {
      const date = new Date(this.date)
      date.setDate(date.getDate() - days + 1)
      this.date = date
      this.fetchMaps()
    }
  }
}
</script>

<style scoped lang="scss">
.controls-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.375em;
  > * {
    flex: 0.3;
  }
  .date-select {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .buttons-contain {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    .dropdown {
      border-radius: 0.5em;
      background: $backgroundColour;
      position: absolute;
      top: 130%;
      right: 0;
      text-align: right;
      z-index: -999;
      width: 120px;
      border: 1px solid $borderColour;
      opacity: 0;
      transition: all 0.3s;
      &.show {
        z-index: 999;
        opacity: 1;
      }
      .item {
        background-color: $backgroundColour;
        padding: 6px 12px;
        cursor: pointer;
        border-bottom: 1px solid $borderColour;
        &:hover {
          background: $secondary2;
        }
        &:first-child {
          border-top-left-radius: 0.5em;
          border-top-right-radius: 0.5em;
          border-top: 0;
        }
        &:last-child {
          border-bottom: 0;
          border-bottom-right-radius: 0.5em;
          border-bottom-left-radius: 0.5em;
        }
      }
    }
  }
  @media (max-width: 768px) {
    flex-direction: column;
    gap: 1em;
  }
}
.grid {
  position: relative;
  background: $primary2;
  max-height: 50vh;
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