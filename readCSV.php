<?php
error_reporting(E_ERROR | E_PARSE);

$file_name = "Canadian Candidates Social Media Ad Spend.csv";
$response = [];

try {
    $row = 2;
    if (($handle = fopen($file_name, "r")) !== FALSE) {

        // Get header info
        $row = fgetcsv($handle);
        $fields = $row;
        $party_index = array_search("Party", $row);
        $facebook_index = array_search("Facebook Spend", $row);
        $twitter_index = array_search("Twitter Spend", $row);
        $parties = [];

        // Get row/candidate info
        while (($row = fgetcsv($handle)) !== FALSE) {
            $party = $row[$party_index];
            $parties[$party]["facebook_spending"] += $row[$facebook_index];
            $parties[$party]["twitter_spending"] += $row[$twitter_index];
            $total_spending  = $row[$facebook_index] + $row[$twitter_index];
            $parties[$party]["total_spending"] += $total_spending;
            $parties[$party]["contributing_spending"][] = $total_spending;

            $response["data"][] = array_combine($fields, $row);
        }

        // Do calculations per party
        foreach ($parties as $name => &$party) {
            $candidate_count = count($party["contributing_spending"]);
            $total_spending = $party["total_spending"];
            $party["mean_spending"] = $total_spending / $candidate_count;

            asort($party["contributing_spending"]);
            $contributing_spending = array_values($party["contributing_spending"]);

            $halved = ($candidate_count/2) - 1;
            if (floor($halved) == $halved) {
                $party["medium_spending"] = ($contributing_spending[$halved] + $contributing_spending[$halved+1])/2;
            } else {
                $party["medium_spending"] = $contributing_spending[ceil($halved)];
            }

            $img_src = null;
            switch($name) {
                case "Liberal":
                    $img_src = "https://pbs.twimg.com/profile_images/1280206591288016896/sIitdJsA_400x400.jpg";
                    break;
                case "Conservative":
                    $img_src = "https://images.glaciermedia.ca/polopoly_fs/1.24121235.1587424083!/fileImage/httpImage/image.jpg_gen/derivatives/landscape_804/1.jpg";
                    break;
                case "Green":
                    $img_src = "https://pbs.twimg.com/profile_images/1298647859580088321/2akKnE4Q_400x400.jpg";
                    break;
                case "NDP":
                    $img_src = "https://pbs.twimg.com/profile_images/1196465881087455232/hFKPjmeb_400x400.png";
                    break;
                case "Bloc Quebecois":
                    $img_src = "https://pbs.twimg.com/profile_images/1339989389112426499/PY4DfAUG_400x400.jpg";
                    break;
                default:
                    break;
            }
            $party["img_src"] = $img_src;
        }

        fclose($handle);
    } else {
        $response["error"] = "Count not open csv file.";
    }

    $response["fields"] = $fields;
    $response["parties"] = $parties;
} catch (Exception $e) {
    // log error
    $response["error"] = "Unexpected error. Please try again later and contact support if error continues.";
}
echo json_encode($response);