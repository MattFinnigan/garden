
export const clone = (obj) => {
  return JSON.parse(JSON.stringify(obj))
}

export const isEmpty = (obj) => {
  return Object.keys(obj).length === 0
}

export const isOverlapping = (el1, el2) => {
  const rect1 = el1.getBoundingClientRect()
  const rect2 = el2.getBoundingClientRect()
  return !(
    rect1.right < rect2.left ||
    rect1.left > rect2.right ||
    rect1.bottom < rect2.top ||
    rect1.top > rect2.bottom
  )
}
export const arrangePlantsInBedWithOverlapCheck = (lastEntry, bed, zoom = 1) => {
  const res = []
  const bedEl = document.createElement('div')
  bedEl.classList.add('bed-container-poscheck')
  bedEl.style.width = `${bed.l * zoom}px`
  bedEl.style.height = `${bed.w * zoom}px`
  bedEl.style.position = 'relative'
  bedEl.style.backgroundColor = 'black'
  document.body.appendChild(bedEl)

  let x = 0
  let y = 0
  const step = 5 // Increment step for x and y when moving to find space

  for (let i = 0; i < lastEntry.qty; i++) {
    const plantEl = document.createElement('div')
    plantEl.classList.add('bed-plant-poscheck')
    plantEl.style.width = `${lastEntry.spacing_x}px`
    plantEl.style.height = `${lastEntry.spacing_y}px`
    plantEl.style.position = 'absolute'
    plantEl.style.backgroundColor = 'green'

    // Find a position that does not overlap
    let placed = false
    while (!placed) {
      plantEl.style.left = `${x}px`
      plantEl.style.top = `${y}px`
      bedEl.appendChild(plantEl) // Temporarily append to check overlap

      const isOverlappingAny = Array.from(bedEl.children).some((existingPlant) => {
        return existingPlant !== plantEl && isOverlapping(plantEl, existingPlant)
      })
      
      if (isOverlappingAny) {
        bedEl.removeChild(plantEl) // Remove to retry placement
        x += step
        if (x + lastEntry.spacing_x >= bed.l) {
          x = 0
          y += step
        }
        if (y + lastEntry.spacing_y > bed.w) {
          console.log(res, y, lastEntry.spacing_y, bed.w, x, lastEntry.spacing_x, bed.l)
          console.log('y: ' + y + ' lastEntry.spacing_y: ' + lastEntry.spacing_y + ' bed.w: ' + bed.w)
          console.log('x: ' + x + ' lastEntry.spacing_x: ' + lastEntry.spacing_x + ' bed.l: ' + bed.l)
          console.warn("Not enough space to place all plants.")
          bedEl.remove()
          return null // Stop placement if no space is left
        }
      } else {
        placed = true
      }
    }

    res.push({ x, y })
  }
  bedEl.remove()
  return res
}

export const draggable = (el, relativeEl, start, update, end, constrain = false) => {
  let isDragging = false
  let clickOffsetX = 0
  let clickOffsetY = 0
  let zoomFactor = document.querySelector('#grid').style.zoom || 1
  const calculatePosition = (e, parentRect, zoomFactor) => {
    const x = ((e.clientX - parentRect.left) / zoomFactor) - clickOffsetX + relativeEl.scrollLeft
    const y = ((e.clientY - parentRect.top) / zoomFactor) - clickOffsetY + relativeEl.scrollTop
    return { x, y }
  }

  const onMouseMove = (e) => {
    if (!isDragging) return
    // find the parent element via dom and get the zoom factor
    relativeEl = document.querySelector('#' + relativeEl.getAttribute('id'))
    zoomFactor = document.querySelector('#grid').style.zoom || 1
    const parentRect = relativeEl.getBoundingClientRect()
    const { x, y } = calculatePosition(e, parentRect, zoomFactor)

    let constrainedX = x
    let constrainedY = y

    if (constrain) {
      const rect = el.getBoundingClientRect()
      const maxX = (relativeEl.offsetWidth) - (rect.width / zoomFactor)
      const maxY = (relativeEl.offsetHeight) - (rect.height / zoomFactor)
      constrainedX = Math.max(0, Math.min(maxX, x))
      constrainedY = Math.max(0, Math.min(maxY, y))
    } else {
      // Constrain to parent's top and left boundaries only
      constrainedX = Math.max(0, x)
      constrainedY = Math.max(0, y)
    }

    update({ x: constrainedX, y: constrainedY })
  }

  const onMouseUp = (e) => {
    if (!isDragging) return
    e.preventDefault()
    isDragging = false
    document.removeEventListener('mousemove', onMouseMove)
    document.removeEventListener('mouseup', onMouseUp)

    const parentRect = relativeEl.getBoundingClientRect()
    const { x, y } = calculatePosition(e, parentRect, zoomFactor)

    end({ x, y })
  }

  el.addEventListener('mousedown', (e) => {
    if (e.target !== el) return

    const elRect = el.getBoundingClientRect()
    const parentRect = relativeEl.getBoundingClientRect()

    clickOffsetX = (e.clientX - elRect.left) / zoomFactor
    clickOffsetY = (e.clientY - elRect.top) / zoomFactor

    isDragging = true

    const startX = ((elRect.left - parentRect.left) + relativeEl.scrollLeft) / zoomFactor
    const startY = ((elRect.top - parentRect.top) + relativeEl.scrollTop) / zoomFactor

    start({ x: startX, y: startY })

    document.addEventListener('mousemove', onMouseMove)
    document.addEventListener('mouseup', onMouseUp)
  })
}


export const localeDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

export const watchScreenSize = (callback) => {
  const resizeObserver = new ResizeObserver(() => {
    callback()
  })
  resizeObserver.observe(document.body)
}