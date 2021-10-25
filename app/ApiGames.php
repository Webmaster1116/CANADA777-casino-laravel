<?php 
namespace VanguardLTE
{
    class ApiGames extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'api_games';
        protected $hidden = [
            'created_at', 
            'updated_at'
        ];
        protected $fillable = [
            'game_id', 
            'name', 
            'category',
            'subcategory', 
            'new', 
            'system', 
            'position', 
            'type', 
            'image', 
            'image_preview', 
            'image_filled',
            'mobile',
            'play_for_fun_supported'
        ];
        public static function boot()
        {
            parent::boot();
        }
        public $labels = [
            'new' => 'New', 
            'exclusive' => 'Exclusive', 
            'top' => 'Top', 
            'hot' => 'Hot', 
            'table' => 'Table'
        ];
        public function getCategoryWagerPercent()
        {
            $wagerperncet = 100;
            $category = null;
            $gameCates = $this->hasMany('VanguardLTE\GameCategory', 'game_id')->get();
            for($i = 0; $i < count($gameCates); $i++){
                $gameCate = $gameCates[$i];
                if ($gameCate != null){
                    $category = $gameCate->category()->first();
                    if($category != null && $category->title != "Hot" && $category->title != "New"){
                        $wagerperncet = $category->wager_percent;
                        break;
                    }
                }
            }
            return $wagerperncet;
        }
        public function categoryy()
        {
            return $this->belongsTo('VanguardLTE\Category');
        }
    }

}
