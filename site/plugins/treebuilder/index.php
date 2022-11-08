<?php

// <https://getkirby.com/docs/cookbook/extensions/subpage-builder#creating-subpages-if-the-pages-already-exists__using-a-route-after-hook>

  function buildPageTree($page) {

      // get the subpage_builder definition from the blueprint
      $builder = $page->blueprint()->subpage_builder();

      // check if anything is set
      if (empty($builder) === false) {

          foreach($builder as $build) {

              // check if all fields have been defined
              $missing = A::missing($build, ['title', 'template', 'uid']);

              // if any is missing, skip it
              if (empty($missing) === false) {
                  continue;
              }

              try {
                  $subpage = $page->createChild([
                      'content' => [
                          'title' => $build['title']
                      ],
                      'slug' => $build['uid'],
                      'template' => $build['template']
                  ]);
              } catch (Exception $error) {
                  throw new Exception($error);
              }

              // publish subpage
              if ($subpage) {

                  // call func recursively
                  buildPageTree($subpage);

                  // publish subpage and sort

                  $subpage->publish();

                  if (isset($build['num']) === true) {
                      $subpage->changeSort($build['num']);
                  }
              }

          }

      }

  }
