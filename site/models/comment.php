<?php

class CommentPage extends Kirby\Cms\Page {

    // write / update comment
    public function writeContent(array $data, string $languageCode = null): bool {

        unset($data['title']);

        // if comment with this slug is found, update it
        if ($comment = Db::first('comment', '*'. ['slug' => $this->slug()])) {

            return Db::update('comment', $data, ['slug' => $this->slug()]);

        } else {
            // else create new comment with this slug

            $data['slug'] = $this->slug();
            return Db::insert('comment', $data);

        }

    }

    // delete comment
    public function delete(bool $force = false): bool {

        return Db::delete('comment', ['slug' => $this->slug()]);

    }

    public function title(): Kirby\Cms\Field {

        // we make up a title for each comment for the Panel
        // from reading he first 100 char of the comment itself
        // this is purely for kirby panel UI reasons
        return $this->text()->excerpt(100)->or('New comment');

    }

    public function changeSlug(string $slug, string $languageCode = null) {

        $slug = Str::slug($slug);

        $data['slug'] = $slug;

        if ($comment = Db::first('comment', '*', ['slug' => $this->slug()])) {

            if (Db::update('comment', $data, ['slug' => $this->slug()])) {
                
                return $this;

            };
        }

        return $this;

    }


    protected function changeStatusToDraft() {

        $data['status'] = 'null';

        if ($comment = Db::first('comment', '*', ['slug' => $this->slug()])) {

            return Db::update('comment', $data, ['slug' => $this->slug()]);

        }

        return $this;

    }

    protected function changeStatusToListed(int $position = null) {

        // create a sorting number for the page
        $num = $this->createNum($position);

        // don't sort if not necessary
        if ($this->status() === 'listed' && $num === $this->num()) {
            return $this;
        }

        $data['status'] = 'listed';

        if ($comment = Db::first('comment', '*', ['slug' => $this->slug()])) {

            return Db::update('comment', $data, ['slug' => $this->slug()]);

        }

        // TODO why should it be `default`
        if ($this->blueprint()->num() === 'default') {
            $this->resortSiblignsAfterListing($num);
        }

        return $this;

    }

    protected function changeStatusToUnlisted() {

        if ($this->status() === 'unlisted') {
            return $this;
        }

        $data['status'] = 'listed';

        if ($comment = Db::first('comment', '*', ['slug' => $this->slug()])) {
            return Db::update('comment', $data, ['slug' => $this->slug()]);
        }

        $this->resortSiblignsAfterUnlisting();

        return $this;

    }

    // public function changeTitle(string $title, string $languageCode = null) {

    //     $data['title'] = $title;

    //     if ($comment = Db::first('comment', '*', ['slug' => $this->slug()])) {

    //         if (Db::update('comment', $data, ['slug' => $this->slug()])) {
    //             return $this;
    //         };
    //     }

    //     return $this

    // }

    public function isDraft(): bool {

        return in_array($this->content()->status(), ['listed', 'unlisted']) === false;

    }

        


}
