<?php 
namespace VanguardLTE
{
    class Game extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'games';
        protected $hidden = [
            'created_at', 
            'updated_at'
        ];
        protected $fillable = [
            'name', 
            'title', 
            'order_no', 
            'shop_id', 
            'gamebank', 
            'percent', 
            'original_id', 
            'jpg_id', 
            'label', 
            'garant_win1', 
            'garant_win3', 
            'garant_win5', 
            'garant_win7', 
            'garant_win9', 
            'garant_win10', 
            'garant_bonus1', 
            'garant_bonus3', 
            'garant_bonus5', 
            'garant_bonus7', 
            'garant_bonus9', 
            'garant_bonus10', 
            'rezerv', 
            'winline1', 
            'winline3', 
            'winline5', 
            'winline7', 
            'winline9', 
            'winline10', 
            'winbonus1', 
            'winbonus3', 
            'winbonus5', 
            'winbonus7', 
            'winbonus9', 
            'winbonus10', 
            'device', 
            'cask', 
            'advanced', 
            'garant_win_bonus1', 
            'garant_win_bonus3', 
            'garant_win_bonus5', 
            'garant_win_bonus7', 
            'garant_win_bonus9', 
            'garant_win_bonus10', 
            'winline_bonus1', 
            'winline_bonus3', 
            'winline_bonus5', 
            'winline_bonus7', 
            'winline_bonus9', 
            'winline_bonus10', 
            'view', 
            'bids', 
            'gameline', 
            'monitor', 
            'bet', 
            'denomination', 
            'scaleMode', 
            'numFloat', 
            'slotViewState', 
            'stat_in', 
            'stat_out', 
            'ReelsMath', 
            'in_game', 
            'in_jpg', 
            'in_jps', 
            'profit', 
            'rtp'
        ];
        public static $values = [
            'jp_1_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_2_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_3_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_4_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_5_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_6_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_7_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_8_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_9_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'jp_10_percent' => [
                '1', 
                '0.9', 
                '0.8', 
                '0.7', 
                '0.6', 
                '0.5', 
                '0.4', 
                '0.3', 
                '0.2', 
                '0.1'
            ], 
            'bet' => [
                '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100', 
                '10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 400, 500, 600, 700, 800, 900, 1000', 
                '100, 200, 300, 400, 500, 600, 700, 800, 900, 1000, 2000, 3000, 4000, 5000, 6000, 7000, 8000, 9000, 10000', 
                '0.10, 0.20, 0.30, 0.40, 0.50, 0.60, 0.70, 0.80, 0.90, 1.00, 5.00, 10.00', 
                '0.10, 0.50, 1.00, 5.00, 10.00, 50.00, 100.00, 500.00, 1000.00', 
                '0.10, 0.50, 1, 5, 10', 
                '0.01, 0.02, 0.05, 0.10, 0.20'
            ], 
            'winline1' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline3' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline5' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline7' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline9' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline10' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win1' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win3' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win5' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win7' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win9' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win10' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winbonus1' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'winbonus3' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'winbonus5' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'winbonus7' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'winbonus9' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'winbonus10' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'garant_bonus1' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'garant_bonus3' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'garant_bonus5' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'garant_bonus7' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'garant_bonus9' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'garant_bonus10' => [
                1, 
                50, 
                100, 
                200, 
                500, 
                1000
            ], 
            'winline_bonus1' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline_bonus3' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline_bonus5' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline_bonus7' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline_bonus9' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'winline_bonus10' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win_bonus1' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win_bonus3' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win_bonus5' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win_bonus7' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win_bonus9' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'garant_win_bonus10' => [
                1, 
                5, 
                10, 
                20, 
                50, 
                100
            ], 
            'match_winline1' => [
                '9, 6, 9, 15, 6, 7, 1, 8, 5, 16, 18, 8, 12, 13, 13, 1, 5, 6, 16, 1, 1, 2, 5, 3, 2, 50, 1, 1, 1, 5, 5, 2, 3, 1, 5, 30, 25, 1, 2, 3, 4, 5, 6, 7, 10', 
                '8, 7, 5, 2, 2, 6, 1, 2, 3, 2, 1, 10, 9, 9, 1, 10, 5, 7, 3, 8, 2, 5, 3, 1, 6, 10, 15, 20, 30, 5, 5, 5, 10, 10, 15, 12, 8, 9, 4, 1, 40, 25, 1, 1', 
                '17, 9, 3, 15, 4, 26, 7, 6, 8, 23, 18, 14, 25, 27, 12, 2, 29, 11, 16, 21, 30, 22, 10, 11, 28, 30, 30, 40, 50, 25, 1, 5, 5, 1, 1, 1, 5, 2, 50, 60'
            ], 
            'match_winline3' => [
                '9, 6, 9, 15, 6, 7, 1, 8, 5, 16, 18, 8, 12, 13, 13, 1, 5, 6, 16, 1, 1, 2, 5, 3, 2, 50, 1, 1, 1, 5, 5, 2, 3, 1, 5, 30, 25, 1, 2, 3, 4, 5, 6, 7, 10', 
                '8, 7, 5, 2, 2, 6, 1, 2, 3, 2, 1, 10, 9, 9, 1, 10, 5, 7, 3, 8, 2, 5, 3, 1, 6, 10, 15, 20, 30, 5, 5, 5, 10, 10, 15, 12, 8, 9, 4, 1, 40, 25, 1, 1', 
                '17, 9, 3, 15, 4, 26, 7, 6, 8, 23, 18, 14, 25, 27, 12, 2, 29, 11, 16, 21, 30, 22, 10, 11, 28, 30, 30, 40, 50, 25, 1, 5, 5, 1, 1, 1, 5, 2, 50, 60'
            ], 
            'match_winline5' => [
                '9, 6, 9, 15, 6, 7, 1, 8, 5, 16, 18, 8, 12, 13, 13, 1, 5, 6, 16, 1, 1, 2, 5, 3, 2, 50, 1, 1, 1, 5, 5, 2, 3, 1, 5, 30, 25, 1, 2, 3, 4, 5, 6, 7, 10', 
                '8, 7, 5, 2, 2, 6, 1, 2, 3, 2, 1, 10, 9, 9, 1, 10, 5, 7, 3, 8, 2, 5, 3, 1, 6, 10, 15, 20, 30, 5, 5, 5, 10, 10, 15, 12, 8, 9, 4, 1, 40, 25, 1, 1', 
                '17, 9, 3, 15, 4, 26, 7, 6, 8, 23, 18, 14, 25, 27, 12, 2, 29, 11, 16, 21, 30, 22, 10, 11, 28, 30, 30, 40, 50, 25, 1, 5, 5, 1, 1, 1, 5, 2, 50, 60'
            ], 
            'match_winline7' => [
                '9, 6, 9, 15, 6, 7, 1, 8, 5, 16, 18, 8, 12, 13, 13, 1, 5, 6, 16, 1, 1, 2, 5, 3, 2, 50, 1, 1, 1, 5, 5, 2, 3, 1, 5, 30, 25, 1, 2, 3, 4, 5, 6, 7, 10', 
                '8, 7, 5, 2, 2, 6, 1, 2, 3, 2, 1, 10, 9, 9, 1, 10, 5, 7, 3, 8, 2, 5, 3, 1, 6, 10, 15, 20, 30, 5, 5, 5, 10, 10, 15, 12, 8, 9, 4, 1, 40, 25, 1, 1', 
                '17, 9, 3, 15, 4, 26, 7, 6, 8, 23, 18, 14, 25, 27, 12, 2, 29, 11, 16, 21, 30, 22, 10, 11, 28, 30, 30, 40, 50, 25, 1, 5, 5, 1, 1, 1, 5, 2, 50, 60'
            ], 
            'match_winline9' => [
                '9, 6, 9, 15, 6, 7, 1, 8, 5, 16, 18, 8, 12, 13, 13, 1, 5, 6, 16, 1, 1, 2, 5, 3, 2, 50, 1, 1, 1, 5, 5, 2, 3, 1, 5, 30, 25, 1, 2, 3, 4, 5, 6, 7, 10', 
                '8, 7, 5, 2, 2, 6, 1, 2, 3, 2, 1, 10, 9, 9, 1, 10, 5, 7, 3, 8, 2, 5, 3, 1, 6, 10, 15, 20, 30, 5, 5, 5, 10, 10, 15, 12, 8, 9, 4, 1, 40, 25, 1, 1', 
                '17, 9, 3, 15, 4, 26, 7, 6, 8, 23, 18, 14, 25, 27, 12, 2, 29, 11, 16, 21, 30, 22, 10, 11, 28, 30, 30, 40, 50, 25, 1, 5, 5, 1, 1, 1, 5, 2, 50, 60'
            ], 
            'match_winline10' => [
                '9, 6, 9, 15, 6, 7, 1, 8, 5, 16, 18, 8, 12, 13, 13, 1, 5, 6, 16, 1, 1, 2, 5, 3, 2, 50, 1, 1, 1, 5, 5, 2, 3, 1, 5, 30, 25, 1, 2, 3, 4, 5, 6, 7, 10', 
                '8, 7, 5, 2, 2, 6, 1, 2, 3, 2, 1, 10, 9, 9, 1, 10, 5, 7, 3, 8, 2, 5, 3, 1, 6, 10, 15, 20, 30, 5, 5, 5, 10, 10, 15, 12, 8, 9, 4, 1, 40, 25, 1, 1', 
                '17, 9, 3, 15, 4, 26, 7, 6, 8, 23, 18, 14, 25, 27, 12, 2, 29, 11, 16, 21, 30, 22, 10, 11, 28, 30, 30, 40, 50, 25, 1, 5, 5, 1, 1, 1, 5, 2, 50, 60'
            ], 
            'match_winbonus1' => [
                '111, 102, 110, 59, 111, 111, 93, 122, 127, 113, 106, 52, 85, 128, 68, 52, 94, 94, 141, 101, 10, 20, 30, 50, 60, 100', 
                '162, 69, 80, 107, 43, 114, 149, 187, 71, 104, 17, 21, 141, 123, 16, 129, 109, 87, 30, 100, 50, 80, 95, 33, 76, 82, 20', 
                '220, 266, 256, 190, 116, 139, 117, 109, 225, 164, 251, 240, 250, 116, 253, 104, 161, 142, 129, 293, 40, 50, 80, 100, 400'
            ], 
            'match_winbonus3' => [
                '111, 102, 110, 59, 111, 111, 93, 122, 127, 113, 106, 52, 85, 128, 68, 52, 94, 94, 141, 101, 10, 20, 30, 50, 60, 100', 
                '162, 69, 80, 107, 43, 114, 149, 187, 71, 104, 17, 21, 141, 123, 16, 129, 109, 87, 30, 100, 50, 80, 95, 33, 76, 82, 20', 
                '220, 266, 256, 190, 116, 139, 117, 109, 225, 164, 251, 240, 250, 116, 253, 104, 161, 142, 129, 293, 40, 50, 80, 100, 400'
            ], 
            'match_winbonus5' => [
                '111, 102, 110, 59, 111, 111, 93, 122, 127, 113, 106, 52, 85, 128, 68, 52, 94, 94, 141, 101, 10, 20, 30, 50, 60, 100', 
                '162, 69, 80, 107, 43, 114, 149, 187, 71, 104, 17, 21, 141, 123, 16, 129, 109, 87, 30, 100, 50, 80, 95, 33, 76, 82, 20', 
                '220, 266, 256, 190, 116, 139, 117, 109, 225, 164, 251, 240, 250, 116, 253, 104, 161, 142, 129, 293, 40, 50, 80, 100, 400'
            ], 
            'match_winbonus7' => [
                '111, 102, 110, 59, 111, 111, 93, 122, 127, 113, 106, 52, 85, 128, 68, 52, 94, 94, 141, 101, 10, 20, 30, 50, 60, 100', 
                '162, 69, 80, 107, 43, 114, 149, 187, 71, 104, 17, 21, 141, 123, 16, 129, 109, 87, 30, 100, 50, 80, 95, 33, 76, 82, 20', 
                '220, 266, 256, 190, 116, 139, 117, 109, 225, 164, 251, 240, 250, 116, 253, 104, 161, 142, 129, 293, 40, 50, 80, 100, 400'
            ], 
            'match_winbonus9' => [
                '111, 102, 110, 59, 111, 111, 93, 122, 127, 113, 106, 52, 85, 128, 68, 52, 94, 94, 141, 101, 10, 20, 30, 50, 60, 100', 
                '162, 69, 80, 107, 43, 114, 149, 187, 71, 104, 17, 21, 141, 123, 16, 129, 109, 87, 30, 100, 50, 80, 95, 33, 76, 82, 20', 
                '220, 266, 256, 190, 116, 139, 117, 109, 225, 164, 251, 240, 250, 116, 253, 104, 161, 142, 129, 293, 40, 50, 80, 100, 400'
            ], 
            'match_winbonus10' => [
                '111, 102, 110, 59, 111, 111, 93, 122, 127, 113, 106, 52, 85, 128, 68, 52, 94, 94, 141, 101, 10, 20, 30, 50, 60, 100', 
                '162, 69, 80, 107, 43, 114, 149, 187, 71, 104, 17, 21, 141, 123, 16, 129, 109, 87, 30, 100, 50, 80, 95, 33, 76, 82, 20', 
                '220, 266, 256, 190, 116, 139, 117, 109, 225, 164, 251, 240, 250, 116, 253, 104, 161, 142, 129, 293, 40, 50, 80, 100, 400'
            ], 
            'match_winline_bonus1' => [
                '9, 6, 9, 15, 6, 7, 1, 8, 5, 16, 18, 8, 12, 13, 13, 1, 5, 6, 16, 1, 1, 2, 5, 3, 2, 50, 1, 1, 1, 5, 5, 2, 3, 1, 5, 30, 25, 1, 2, 3, 4, 5, 6, 7, 10', 
                '8, 7, 5, 2, 2, 6, 1, 2, 3, 2, 1, 10, 9, 9, 1, 10, 5, 7, 3, 8, 2, 5, 3, 1, 6, 10, 15, 20, 30, 5, 5, 5, 10, 10, 15, 12, 8, 9, 4, 1, 40, 25, 1, 1', 
                '17, 9, 3, 15, 4, 26, 7, 6, 8, 23, 18, 14, 25, 27, 12, 2, 29, 11, 16, 21, 30, 22, 10, 11, 28, 30, 30, 40, 50, 25, 1, 5, 5, 1, 1, 1, 5, 2, 50, 60'
            ], 
            'match_winline_bonus3' => [
                '9, 6, 9, 15, 6, 7, 1, 8, 5, 16, 18, 8, 12, 13, 13, 1, 5, 6, 16, 1, 1, 2, 5, 3, 2, 50, 1, 1, 1, 5, 5, 2, 3, 1, 5, 30, 25, 1, 2, 3, 4, 5, 6, 7, 10', 
                '8, 7, 5, 2, 2, 6, 1, 2, 3, 2, 1, 10, 9, 9, 1, 10, 5, 7, 3, 8, 2, 5, 3, 1, 6, 10, 15, 20, 30, 5, 5, 5, 10, 10, 15, 12, 8, 9, 4, 1, 40, 25, 1, 1', 
                '17, 9, 3, 15, 4, 26, 7, 6, 8, 23, 18, 14, 25, 27, 12, 2, 29, 11, 16, 21, 30, 22, 10, 11, 28, 30, 30, 40, 50, 25, 1, 5, 5, 1, 1, 1, 5, 2, 50, 60'
            ], 
            'match_winline_bonus5' => [
                '9, 6, 9, 15, 6, 7, 1, 8, 5, 16, 18, 8, 12, 13, 13, 1, 5, 6, 16, 1, 1, 2, 5, 3, 2, 50, 1, 1, 1, 5, 5, 2, 3, 1, 5, 30, 25, 1, 2, 3, 4, 5, 6, 7, 10', 
                '8, 7, 5, 2, 2, 6, 1, 2, 3, 2, 1, 10, 9, 9, 1, 10, 5, 7, 3, 8, 2, 5, 3, 1, 6, 10, 15, 20, 30, 5, 5, 5, 10, 10, 15, 12, 8, 9, 4, 1, 40, 25, 1, 1', 
                '17, 9, 3, 15, 4, 26, 7, 6, 8, 23, 18, 14, 25, 27, 12, 2, 29, 11, 16, 21, 30, 22, 10, 11, 28, 30, 30, 40, 50, 25, 1, 5, 5, 1, 1, 1, 5, 2, 50, 60'
            ], 
            'match_winline_bonus7' => [
                '9, 6, 9, 15, 6, 7, 1, 8, 5, 16, 18, 8, 12, 13, 13, 1, 5, 6, 16, 1, 1, 2, 5, 3, 2, 50, 1, 1, 1, 5, 5, 2, 3, 1, 5, 30, 25, 1, 2, 3, 4, 5, 6, 7, 10', 
                '8, 7, 5, 2, 2, 6, 1, 2, 3, 2, 1, 10, 9, 9, 1, 10, 5, 7, 3, 8, 2, 5, 3, 1, 6, 10, 15, 20, 30, 5, 5, 5, 10, 10, 15, 12, 8, 9, 4, 1, 40, 25, 1, 1', 
                '17, 9, 3, 15, 4, 26, 7, 6, 8, 23, 18, 14, 25, 27, 12, 2, 29, 11, 16, 21, 30, 22, 10, 11, 28, 30, 30, 40, 50, 25, 1, 5, 5, 1, 1, 1, 5, 2, 50, 60'
            ], 
            'match_winline_bonus9' => [
                '9, 6, 9, 15, 6, 7, 1, 8, 5, 16, 18, 8, 12, 13, 13, 1, 5, 6, 16, 1, 1, 2, 5, 3, 2, 50, 1, 1, 1, 5, 5, 2, 3, 1, 5, 30, 25, 1, 2, 3, 4, 5, 6, 7, 10', 
                '8, 7, 5, 2, 2, 6, 1, 2, 3, 2, 1, 10, 9, 9, 1, 10, 5, 7, 3, 8, 2, 5, 3, 1, 6, 10, 15, 20, 30, 5, 5, 5, 10, 10, 15, 12, 8, 9, 4, 1, 40, 25, 1, 1', 
                '17, 9, 3, 15, 4, 26, 7, 6, 8, 23, 18, 14, 25, 27, 12, 2, 29, 11, 16, 21, 30, 22, 10, 11, 28, 30, 30, 40, 50, 25, 1, 5, 5, 1, 1, 1, 5, 2, 50, 60'
            ], 
            'match_winline_bonus10' => [
                '9, 6, 9, 15, 6, 7, 1, 8, 5, 16, 18, 8, 12, 13, 13, 1, 5, 6, 16, 1, 1, 2, 5, 3, 2, 50, 1, 1, 1, 5, 5, 2, 3, 1, 5, 30, 25, 1, 2, 3, 4, 5, 6, 7, 10', 
                '8, 7, 5, 2, 2, 6, 1, 2, 3, 2, 1, 10, 9, 9, 1, 10, 5, 7, 3, 8, 2, 5, 3, 1, 6, 10, 15, 20, 30, 5, 5, 5, 10, 10, 15, 12, 8, 9, 4, 1, 40, 25, 1, 1', 
                '17, 9, 3, 15, 4, 26, 7, 6, 8, 23, 18, 14, 25, 27, 12, 2, 29, 11, 16, 21, 30, 22, 10, 11, 28, 30, 30, 40, 50, 25, 1, 5, 5, 1, 1, 1, 5, 2, 50, 60'
            ], 
            'rezerv' => [
                2, 
                4, 
                6, 
                8, 
                10
            ], 
            'cask' => [
                9, 
                18, 
                36, 
                72, 
                90
            ], 
            'denomination' => [
                '0.01', 
                '0.02', 
                '0.05', 
                '0.10', 
                '0.20', 
                '0.25', 
                '0.50', 
                '1.00', 
                '2.00', 
                '2.50', 
                '5.00', 
                '10.00', 
                '20.00', 
                '25.00', 
                '50.00', 
                '100.00'
            ], 
            'gamebank' => [
                'slots', 
                'little', 
                'table_bank', 
                'fish'
            ]
        ];
        public $shortNames = [
            'Low', 
            'Medium', 
            'High'
        ];
        public $labels = [
            'new' => 'New', 
            'exclusive' => 'Exclusive', 
            'top' => 'Top', 
            'hot' => 'Hot'
        ];
        public $gamebankNames = [
            'slots' => 'Slots', 
            'little' => 'Little', 
            'table_bank' => 'Table', 
            'fish' => 'Fish'
        ];
        public static function boot()
        {
            parent::boot();
        }
        public function get_values($key, $add_empty = false, $add_value = false)
        {
            $arr = Game::$values[$key];
            $labels = $arr;
            if( strpos($key, 'match_winbonus') > -1 || strpos($key, 'match_winline') > -1 || strpos($key, 'match_winline_bonus') > -1 ) 
            {
                $labels = $this->shortNames;
                $add_value = false;
            }
            if( $add_empty ) 
            {
                $array = array_combine(array_merge([''], $arr), array_merge(['---'], $labels));
            }
            else
            {
                $array = array_combine($arr, $labels);
            }
            if( $add_value ) 
            {
                return [$add_value => $add_value] + $array;
            }
            return $array;
        }
        public function shop()
        {
            return $this->belongsTo('VanguardLTE\Shop', 'shop_id');
        }
        public function jpg()
        {
            return JPG::where('shop_id', $this->shop_id)->get();
        }
        public function jackpot()
        {
            return $this->hasOne('VanguardLTE\JPG', 'id', 'jpg_id');
        }
        public function game_win()
        {
            return $this->hasOne('VanguardLTE\GameWin', 'game_id', 'original_id');
        }
        public function game_bank()
        {
            return $this->hasOne('VanguardLTE\GameBank', 'shop_id', 'shop_id');
        }
        public function statistics()
        {
            $shop_id = (\Auth::check() ? \Auth::user()->shop_id : 0);
            return $this->hasMany('VanguardLTE\StatGame', 'game', 'name')->where('shop_id', $shop_id)->orderBy('date_time', 'DESC');
        }
        public function categories()
        {
            return $this->hasMany('VanguardLTE\GameCategory', 'game_id');
        }
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
        public function getCategoryTitle()
        {
            $title = '';
            $category = null;
            $gameCate = $this->hasOne('VanguardLTE\GameCategory', 'game_id')->first();
            if ($gameCate != null){
                $category = $gameCate->category()->first();
            }
            if ($category != null){
                $title = $category->title;
            }
            return $title;
        }
        public function getWagerTime(){
            return 70;
        }
        public function name_ico()
        {
            return explode(' ', $this->name)[0];
        }
        public function get_gamebank($slotState = '')
        {
            if( $slotState == 'bonus' ) 
            {
                return $this->game_bank->bonus;
            }
            if( $this->gamebank != null && $this->game_bank ) 
            {
                return $this->game_bank->{$this->gamebank};
            }
            return 0;
        }
        public function set_gamebank($balance, $type = 'update', $slotState = '')
        {
            if( $this->gamebank != null || $slotState == 'bonus' ) 
            {
                $bank = $this->game_bank;
                $gamebank = $this->gamebank;
                if( $slotState == 'bonus' ) 
                {
                    $gamebank = 'bonus';
                }
                if( !$bank ) 
                {
                    $bank = GameBank::create(['shop_id' => $this->shop_id]);
                }
                if( $type == 'inc' ) 
                {
                    $bank->increment($gamebank, $balance);
                }
                if( $type == 'dec' ) 
                {
                    $bank->decrement($gamebank, $balance);
                }
                if( $type == 'update' ) 
                {
                    $bank->update([$gamebank => $balance]);
                }
            }
        }
        public function add_jps($user = false, $jpid, $sum, $type = 'add')
        {
            $shop = Shop::find($this->shop_id);
            $jpg = $this->jpg();
            $old = $jpg[$jpid]->balance;
            if( !$user ) 
            {
                $user = User::where('role_id', 4)->whereHas('rel_shops', function($query) use ($shop)
                {
                    $query->where('shop_id', $shop->id);
                })->first();
            }
            if( !$shop ) 
            {
                return [
                    'success' => false, 
                    'text' => trans('app.wrong_shop')
                ];
            }
            if( !$sum ) 
            {
                return [
                    'success' => false, 
                    'text' => trans('app.wrong_sum')
                ];
            }
            if( $type == 'add' && $shop->balance < $sum ) 
            {
                return [
                    'success' => false, 
                    'text' => 'Not enough money in the shop "' . $shop->name . '". Only ' . $shop->balance
                ];
            }
            if( $type == 'out' && $jpg[$jpid]->balance < $sum ) 
            {
                return [
                    'success' => false, 
                    'text' => 'Not enough money in the jackpot balance "' . $this->name . '". Only ' . $jpg[$jpid]->balance
                ];
            }
            $open_shift = OpenShift::where([
                'shop_id' => $this->shop_id, 
                'end_date' => null
            ])->first();
            if( !$open_shift ) 
            {
                return [
                    'success' => false, 
                    'text' => trans('app.shift_not_opened')
                ];
            }
            $sum = ($type == 'out' ? -1 * $sum : $sum);
            if( $jpg[$jpid]->balance + $sum < 0 ) 
            {
                return [
                    'success' => false, 
                    'text' => 'Balance < 0'
                ];
            }
            $jpg[$jpid]->update(['balance' => $jpg[$jpid]->balance + $sum]);
            $shop->update(['balance' => $shop->balance - $sum]);
            if( $type == 'out' ) 
            {
                $open_shift->increment('balance_in', abs($sum));
            }
            else
            {
                $open_shift->increment('balance_out', abs($sum));
            }
            if( $user ) 
            {
                BankStat::create([
                    'name' => $this->name . ' JPG ' . $jpid, 
                    'user_id' => $user->id, 
                    'type' => $type, 
                    'sum' => abs($sum), 
                    'old' => $old, 
                    'new' => $jpg[$jpid]->balance, 
                    'shop_id' => $shop->id
                ]);
            }
            return ['success' => true];
        }
    }

}
