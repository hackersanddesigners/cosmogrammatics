function footnotesDesign() {

  function smallScreen(refs, notesWrap, notes) {

    if (window.innerWidth <= 600) { 

      notesWrap.classList.add('hidden')

      if (notes.length > 0) {
        notes.map(note => {
          note.style.display = 'none' 
        })
      }

      if (refs.length > 0) {
        refs.map((ref, idx) => {

          // on click display connected note
          ref.addEventListener('click', (e) => {
            e.preventDefault()

            const id = ref.id.split('-').pop()
            const note = notes.find(note => note.id.split('-').pop() === id)

            // start counting from current li-item, as it is the only one visible
            // and therefore it would otherwise always start from 1
            // note.parentElement.setAttribute('start', idx +1)
            // note.style.setProperty('--footnote-count', idx +1)

            if (note.style.display === 'table-row') {

              note.style.display = 'none'
              notesWrap.removeAttribute('style')

            } else {

              // -- reset
              notes.map(note => {
                note.style.display = 'none' 
              })
              // --
              
              note.style.display = 'table-row'

              notesWrap.style.display = 'block'
              notesWrap.style.position = 'fixed'
              notesWrap.style.bottom = 0
              notesWrap.style.left = 0
              notesWrap.style.backgroundColor = 'white'

            }

          })
        })
      }

    } else {
      bigScreen()
    }
  }

  function bigScreen() {

    if (window.innerWidth >= 600) { 

      // -- reset from small-screen when window-resizing
      notesWrap.classList.remove('hidden')
      notesWrap.removeAttribute('style')

      if (notes.length > 0) {
        notes.map(note => {
          note.removeAttribute('style')
        })
      }
      // --


      // -- set notes-wrap
      //    we set notes-wrap to left => content-block width?
      //    TODO double check this, as the code should be
      //    => `sideColumn.width + contentBlock.width` instead
      let sideColumn = document.querySelector('.view-comment-options-wrapper')
      let sideColumnRect = sideColumn.getBoundingClientRect()

      let contentBlock = document.querySelector('.content-wrapper .row .column')
      let contentBlockRect = contentBlock.getBoundingClientRect()

      notesWrap.style.left = `${contentBlockRect.width}px`

      // -- position notes aligned to footnote-ref
      refs.forEach(ref  => {

        // get footnote-ref top position
        let top = Math.round(ref.getBoundingClientRect().top - notesWrap.getBoundingClientRect().top)

        // get note
        const id = ref.id.split('-').pop()
        const note = notes.find(note => note.id.split('-').pop() === id)

        if (note) {

          // set note to footnote-ref top position
          if (note.previousElementSibling) {
            top = solveFor(note, top, notesWrap.getBoundingClientRect().top)
          }

          note.style.position = 'absolute'
          note.style.top = `${top}px`
          // --

        }

      })

    }
  }

  function solveFor(el, currentTop, parentTop) {
    // get footnote-ref top and handle ref overlapping (eg at the same top pos)

    const previousEl = el.previousElementSibling
    const previousElTop = parseInt(previousEl.style.top, 10)
    const previousElBottom = previousEl.getBoundingClientRect().bottom - parentTop

    // check for overlaps
    if (currentTop === previousElTop) {
      const newTop = currentTop + previousEl.getBoundingClientRect().height
      return newTop
    } else if (currentTop < previousElBottom) {
      const newTop = previousElBottom
      return newTop
    } else {
      return currentTop
    } 

  }


  // -- footnotes init
  const refs = Array.from(document.querySelectorAll('.ref-ft'))
  const notesWrap = document.querySelector('.notes')
  const notes = Array.from(notesWrap.querySelectorAll('.notes li'))

  smallScreen(refs, notesWrap, notes)
  bigScreen(refs, notesWrap, notes)

  window.addEventListener('resize', () => {
    smallScreen(refs, notesWrap, notes)
    bigScreen(refs, notesWrap, notes)
  })
  
}

module.exports = footnotesDesign
