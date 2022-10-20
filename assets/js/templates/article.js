import {
  $,
  SelectionObserver
} from '../utils/index.js'

const article_element    = $( 'main' )
const selection_observer = new SelectionObserver( article_element )



function post_comment( e ) {
  // e.preventDefault()
  console.log( e )
}
