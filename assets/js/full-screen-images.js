module.exports = {
  init() {
    const images = Array.from( document.querySelectorAll( 'main figure img' ) )
    for ( const image of images ) {
      const anchor = image.parentElement
      if  ( anchor ) {
        anchor.onclick = e => {
          e.preventDefault()
          const fullscreen_container = document.createElement( 'div' )
          fullscreen_container.classList.add( "fullscreen" )
          const big_image = image.cloneNode()
          fullscreen_container.appendChild( big_image )
          const close_button = document.createElement( "span" )
          close_button.innerHTML = "x"
          fullscreen_container.appendChild( close_button )
          fullscreen_container.onclick = (f) => {
            fullscreen_container.remove()
            f.preventDefault()
            f.stopImmediatePropagation()
          }
          document.body.appendChild( fullscreen_container )
        }
      }
    }

  }

}
