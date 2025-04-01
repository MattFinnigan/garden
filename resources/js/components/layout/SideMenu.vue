
<template>
  <aside class="side-menu">
    <nav>
      <div class="month-select">
        <Select v-model.number="month" :options="monthOptionsLong"/>
      </div>
      <div class="buttons-contain">
        <Button icon :class="['icon', { accent: viewing === 'plants' }]" @click="viewing = 'plants'"><Icon colour="black" name="seedling" maskSize="22px" size="1.4em"></Icon>Plants</Button>
        <Button icon :class="['icon', { accent: viewing === 'crops' }]" @click="viewing = 'crops'"><Icon colour="black" name="view" maskSize="22px" size="1.4em"></Icon>Crops</Button>
      </div>
      <!-- List items -->
      <div class="list-items">
        <ListItem
          v-show="viewing === 'plants'"
          v-for="plant in plants"
          :key="plant.id"
          :highlight="plant.id === currentPlant?.id"
          @click="selectPlant(plant)">
          <template #image>
            <img :src="'./images/upload/' + plant.image" alt="Plant icon">
          </template>
          <template #title>
            {{ plant.name }}
          </template>
          <template #description>
            <p><em>{{ plant.variety }}</em></p>
            <p>Sow {{ monthsShort[plant.sow_from] }} - {{ monthsShort[plant.sow_to] }}</p>
            <p>Harvest: {{ plant.days_to_harvest }} Days</p>
          </template>
          <template #actions>
            <Button classes="sm icon transparent" @click="editPlant(plant)"><Icon name="edit" colour="black"/></Button>
            <Button classes="sm icon transparent" @click="deletePlant(plant)"><Icon name="delete" colour="black"/></Button>
          </template>
        </ListItem>
        <ListItem
          v-show="viewing === 'crops'"
          v-for="crop in crops"
          :key="crop.id"
          :highlight="crop.id === currentCrop?.id"
          @click="selectCrop(crop)">
          <template #image>
            <img :src="'./images/upload/' + crop.plant.image" alt="Plant icon">
          </template>
          <template #title>
            {{ crop.plant.name }} #{{ crop.id }}
          </template>
          <template #description>
            <p><em>{{ crop.plant.variety }}</em></p>
            <p>{{ monthsShort[crop.startMonth].toUpperCase() }} - {{ monthsShort[crop.endMonth].toUpperCase() }}</p>
            <p>Qty: {{ crop.qty }}</p>
          </template>
          <template #actions>
            <Button classes="sm icon transparent" @click.prevent="editCrop(crop)"><Icon name="edit" colour="black"/></Button>
            <Button classes="sm icon transparent" @click.prevent="deleteCrop(crop)"><Icon name="delete" colour="black"/></Button>
          </template>
        </ListItem>
        <div class="add-button">
          <Button v-show="viewing === 'plants'" class="sm icon primary" @click="createPlant"><Icon name="plus"/></Button>
        </div>
      </div>
    </nav>
    <!-- Modals -->
    <!-- Plant form Modal -->
    <Modal v-if="editingPlant" @close="cancelPlant">
      <template #header>
        <h5>Create a new Plant </h5>
        <p>Let's add a Plant with it's first Entry</p>
      </template>
      <template #content>
        <PlantForm @done="plantSubmitted" @cancel="cancelPlant">
          <template #buttons>
            <Button class="primary" type="submit" >Done</Button>
          </template>
        </PlantForm>
      </template>
    </Modal>
    <!-- Crop form Modal -->
    <Modal v-if="editingCrop" @close="cancelCrop">
      <template #header>
        <h5>Edit Crop</h5>
        <p>Let's edit the Crop</p>
      </template>
      <template #content>
        <CropForm @done="cropSubmitted">
          <template #buttons>
            <Button class="secondary outline" @click="showCropEntries">See Entries</Button>
            <Button class="primary" type="submit">Done</Button>
          </template>
        </CropForm>
      </template>
    </Modal>
    <!-- Crop entry form Modal -->
    <Modal v-if="editingCropEntry" @close="cancelCropEntries">
      <template #header>
        <h5>Crop Entry</h5>
        <p>Let's add a Crop Entry</p>
      </template>
      <template #content>
        <CropEntryForm @done="cropSubmitted">
          <template #buttons>
            <Button class="secondary outline" @click="showCropEntries">Back to Entries</Button>
            <Button class="primary" type="submit">Done</Button>
          </template>
        </CropEntryForm>
      </template>
    </Modal>
    <!-- Crop entries list Modal -->
    <Modal v-else-if="cropEntries !== null" @close="cancelCropEntries">
      <template #header>
        <h5>Crop "{{ currentCrop.plant.name }} #{{ currentCrop.id }}" Entries</h5>
        <p>Find entries & add new ones</p>
      </template>
      <template #content>
        <div class="top-row">
          <Button classes="primary icon sm" @click="newEntry"><Icon name="plus"></Icon></Button>
        </div>
        <Table
          v-show="cropEntriesMapped.length > 0"
          :headers="cropEntryHeaders"
          :rows="cropEntriesMapped"
          :actions="{ delete: true }"
          @delete="(cropEntry) => { deleteCropEntry(cropEntry) }">
        </Table>
        <div v-show="cropEntriesMapped.length === 0" class="empty-state">
          <Icon name="empty" size="4em" colour="grey-300"/>
          <p class="text-center"><em>Looks like you haven't added any Crop Entries for this month.</em></p>
        </div>
      </template>
    </Modal>
    <!-- Delete confirmation Modal -->
    <Modal v-if="deleteConfirm" @close="deleteConfirm = null">
      <template #header>
        <h5>Delete {{ deleteConfirm.name }}</h5>
        <p>Are you sure you want to delete this {{ deleteConfirm.name }}?</p>
      </template>
      <template #content>
        <p>This action cannot be undone.</p>
      </template>
      <template #buttons>
        <Button class="danger outline" @click="() => {
          if (deleteConfirm.name === 'Plant') {
            deletePlant(currentPlant, true)
          } else if (deleteConfirm.name === 'Crop') {
            deleteCrop(currentCrop, true)
          } else {
            deleteCropEntry(deleteConfirm.obj, true)
          }
        }">Yes</Button>
        <Button class="danger" @click="deleteConfirm = null">No</Button>
      </template>
    </Modal>
  </aside>
</template>

<script>
import { monthsShort, monthOptionsLong, defaultCropEntry } from '../../utils/consts'
import { fetchPlants, fetchCrops, deleteCrop, deletePlant, createPlant, deleteCropEntry} from '../../utils/api'
import ListItem from '../layout/ListItem.vue'
import PlantForm from '../forms/PlantForm.vue'
import CropForm from '../forms/CropForm.vue'
import CropEntryForm from '../forms/CropEntryForm.vue'

export default {
  components: {
    PlantForm,
    ListItem,
    CropForm,
    CropEntryForm
  },
  data () {
    return {
      monthOptionsLong: monthOptionsLong(),
      monthsShort: monthsShort(),
      viewing: 'plants',
      mode: 'view',
      deleteConfirm: null,
      cropEntryHeaders: [{ label: 'Date', key: 'datetimestamp' }, { label: 'Stage', key: 'stage' }, { label: 'Action', key: 'action' }, { label: 'Notes', key: 'notes' }]
    }
  },
  mounted () {
    this.month = new Date().getMonth() + 1
    fetchPlants(this)
    fetchCrops(this, this.month)
  },
  computed: {
    currentPlant () {
      return this.$store.state.plants.current
    },
    plants () {
      return this.$store.state.plants.list
    },
    editingPlant () {
      return this.viewing === 'plants' && this.mode === 'edit'
    },
    crops () {
      return this.$store.state.crops.list
    },
    currentCrop () {
      return this.$store.state.crops.current
    },
    editingCrop () {
      return this.viewing === 'crops' && this.mode === 'edit'
    },
    cropEditingMode () {
      return this.$store.state.crops.mode === 'edit'
    },
    cropEntries () {
      return this.$store.state.crop_entries.entriesList
    },
    cropEntriesMapped () {
      return this.cropEntries.map(cropEntry => {
        return {
          id: cropEntry.id,
          stage: `${cropEntry.stage}`,
          action: cropEntry.action,
          datetimestamp: new Date(cropEntry.datetimestamp).toLocaleDateString(),
          notes: cropEntry.notes
        }
      })
    },
    editingCropEntry () {
      return this.cropEntries !== null && this.$store.state.crop_entries.current
    },
    month: {
      get () {
        return this.$store.state.maps.month
      },
      set (value) {
        return this.$store.commit('maps/setMapMonth', value)
      }
    }
  },
  watch: {
    month (val) {
      fetchCrops(this, val)
    },
    viewing (val) {
      if (val === 'crops') {
        this.$store.commit('plants/setCurrentPlant', null)
      } else {
        this.$store.commit('crops/setCurrentCrop', null)
      }
    },
    currentCrop (val) {
      if (val) {
        this.viewing = 'crops'
      }
    },
    cropEditingMode (val) {
      if (val) {
        this.viewing = 'crops'
        this.mode = 'edit'
      }
    },
  },
  methods: {
    selectCrop (crop) {
      this.$store.commit('crops/setCurrentCrop', crop)
    },
    cancelCrop () {
      this.mode = 'view'
      this.$store.commit('crops/setMode', 'view')
    },
    editCrop (crop) {
      this.$store.commit('crops/setCurrentCrop', crop)
      this.mode = 'edit'
      this.$store.commit('crops/setMode', 'edit')
    },
    deleteCrop (crop, confirmed = false) {
      if (confirmed) {
        deleteCrop(this, crop.id).then(() => {
          fetchCrops(this, this.month)
          this.deleteConfirm = null
        })
      } else {
        this.deleteConfirm = {
          name: 'Crop',
          obj: crop
        }
      }
    },
    cropSubmitted (crop) {
      // refresh crop list
      this.$store.commit('crop_entries/setCropEntries', null)
      this.$store.commit('crop_entries/setCurrentCropEntry', null)
      this.$store.commit('crops/setCurrentCrop', null)
      this.$store.commit('crops/setCrops', [])
      fetchCrops(this, this.month).then(() => {
        this.$store.commit('crops/setCurrentCrop', crop)
        this.mode = 'view'
        this.$store.commit('crops/setMode', 'view')
      })
    },
    showCropEntries () {
      this.cancelCrop()
      this.$store.commit('crop_entries/setCurrentCropEntry', null)
      this.$store.commit('crop_entries/setCropEntries', this.currentCrop.crop_entries)
    },
    cancelCropEntries () {
      this.$store.commit('crop_entries/setCropEntries', null)
      this.$store.commit('crop_entries/setCurrentCropEntry', null)
    },
    deleteCropEntry (entry, confirmed = false) {
      if (confirmed) {
        deleteCropEntry(this, entry.id).then((response) => {
          this.deleteConfirm = null
          this.$store.commit('crop_entries/setCropEntries', response.data.crop.crop_entries)
        })
      } else {
        this.deleteConfirm = {
          name: 'Crop Entry',
          obj: entry
        }
      }
    },
    newEntry () {
      this.$store.commit('crop_entries/setCurrentCropEntry', defaultCropEntry(this.currentCrop))
    },
    selectPlant (plant) {
      this.$store.commit('plants/setCurrentPlant', plant)
    },
    cancelPlant () {
      this.mode = 'view'
      this.$store.commit('plants/setCurrentPlant', null)
    },
    createPlant () {
      this.$store.commit('plants/setCurrentPlant', null)
      this.mode = 'edit'
    },
    plantSubmitted (plant) {
      fetchPlants(this).then(() => {
        this.$store.commit('plants/setCurrentPlant', plant)
        this.mode = 'view'
      })
    },
    editPlant (plant) {
      this.$store.commit('plants/setCurrentPlant', plant)
      this.mode = 'edit'
    },
    deletePlant (plant, confirmed = false) {
      if (confirmed) {
        deletePlant(this, plant.id).then(() => {
          fetchPlants(this)
          this.deleteConfirm = null
        })
      } else {
        this.deleteConfirm = {
          name: 'Plant',
          obj: plant
        }
      }
    }
  }
}
</script>
<style lang="scss" scoped>
aside {
  transition: all 0.3s;
  height: calc(100vh - 55px);
  position: relative;
  nav {
    border: 1px solid $grey-200;
    width: 100%;
    height: 100%;
    transition: all 0.3s;
    .month-select {
      padding: 10px;
    }
    .buttons-contain {
      display: flex;
      border-top-right-radius: 10px;
      border-top-left-radius: 10px;
      button {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: $textColour;
        gap: 2px;
        flex: 1;
        border-radius: 0;
        &:first-child {
          border-top-left-radius: 10px;
        }
        &:last-child {
          border-top-right-radius: 10px;
        }
      }
    }
    .list-items {
      margin-bottom: 10px;
      max-height: 375px;
      overflow-y: scroll;
      .add-button {
        display: flex;
        justify-content: flex-end;
        width: 100%;
        position: absolute;
        bottom: 0;
        padding: 10px 0;
        button {
          margin-right: 10px;
        }
      }
    }
  }
  .top-row {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 10px;
  }
  @include device (desktop, 'all') {
    nav {
      width: 250px;
    }
  }
}
</style>
