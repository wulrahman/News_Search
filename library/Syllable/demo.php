<?php
header('Content-type: text/html; charset=utf-8');

$text = "Young people across the UK have had their work, studies and lives upended because of the coronavirus pandemic.

Many are working from home, while others have been furloughed or even made redundant.

There could be more than one million young workers who are without a job, if the overall UK level of unemployment goes up from the current 4% of workers to 10%, according to the Resolution Foundation think-tank.

On top of that, under-30s have been hardest hit by a fall in their income during lockdown as more of their money goes on essentials.

So is there anything the chancellor could announce in his speech on Wednesday to help them?

'A grant for apprentices would be a start'
Emma-Jayne is an apprentice chef from Dorset, earning £5 per hour.

She is one of the many workers who were furloughed in the hospitality sector. The scheme was introduced by the government to minimise coronavirus-related job losses, and it pays 80% of staff salaries up to £2,500 a month.

Although restaurants in England have since been allowed to reopen, Emma-Jayne has only gone back to work on a part-time basis. All of the culinary courses she was due to go on have also been cancelled.

Sunak to give firms £1,000 cash bonus to hire trainees
UK firms slash more than 12,000 jobs in two days
And while part of her level one apprenticeship is based on online coursework, she hasn't been able to make the relevant recipes as her furlough payments \"don't go as far they need to.\"

\"Over the last few months, I've barely made my rent and managed to pay my bills.

\"I can't help but feel let down by the government because there's a big difference between being able to survive and being able to live when it comes to your pay cheque.\"

\"I think a grant for apprentices would be a start to make up for the lack of support,\" she says.

'Carers should be recognised'
Image copyrightNAIRN MCDONALD
Nairn McDonald, 24, is a full-time carer for his mum and 21-year-old brother. He says that during lockdown they've been shielding in place.

\"Now they can't get out, my role has changed. I'm also picking up prescriptions, doing more errands - so it's a bit more labour intensive.\"

Nairn says that costs have also been adding up. He relies on universal credit, the benefit for working-age people in the UK, which he says doesn't \"really go a long way.\"

Although the standard allowance in universal credit has been temporarily increased for 2020-21, Nairn points out that his carer's allowance of about £67 per week is deducted from that. He describes the rise as \"minimal\".

Nairn says he'd like to see additional financial aid for carers, as well as support for those transitioning back into education or work.

\"Some sort of job guarantee - whether it's an apprenticeship, an internship or an interview - would be really helpful for young people like me.\"

'Help should be given to renters too'";
$text = preg_replace('/[^a-z\d]+/i', ' ', $text);

$source = $text;

// phpSyllable code
require_once("src/Syllable.php");
require_once("src/Source/Source.php");
require_once("src/Source/File.php");

require_once("src/Cache/Cache.php");
require_once("src/Cache/File.php");
require_once("src/Cache/Json.php");
require_once("src/Cache/Serialized.php");

require_once("src/Hyphen/Hyphen.php");
require_once("src/Hyphen/Text.php");
require_once("src/Hyphen/Dash.php");
require_once("src/Hyphen/Entity.php");
require_once("src/Hyphen/Soft.php");
require_once("src/Hyphen/ZeroWidthSpace.php");


$language = 'en-us';

$syllable_us = new \Vanderlee\Syllable\Syllable($language);

/** @var \Vanderlee\Syllable\Cache\File $cache */
$cache = $syllable_us->getCache();
$cache->setPath(__DIR__ . '/cache');
$syllable_us->getSource()->setPath(__DIR__ . '/languages');

$syllable_us->setHyphen('-');
$syllable_us_word = nl2br($syllable_us->hyphenateText($source));

$language = 'en-gb';
$syllable_gb = new \Vanderlee\Syllable\Syllable($language);

/** @var \Vanderlee\Syllable\Cache\File $cache */
$cache = $syllable_gb->getCache();
$cache->setPath(__DIR__ . '/cache');
$syllable_gb->getSource()->setPath(__DIR__ . '/languages');

$syllable_gb->setHyphen('-');
$syllable_gb_word = nl2br($syllable_gb->hyphenateText($source));

$syllable_us_words = explode(" ", $syllable_us_word);

$syllable_gb_words = explode(" ", $syllable_gb_word);

foreach($syllable_us_words as $key => $word) {

    $syllable_count_us = count(explode("-", $word));

    $syllable_count_gb = count(explode("-", $syllable_gb_words[$key]));

    if($syllable_count_us  >=  $syllable_count_gb) {

        $syllable_combine_words[$key] = $word;
    }
    else {

        $syllable_combine_words[$key] = $syllable_gb_words[$key];

    }
}

?>

<body>
<h1>phpSyllable</h1>
<h4>PHP Hyphenation library based on Frank Liang's algorithm used in TeX.</h4>

<form method="POST">
    <div>
        <label>
            <select name="language">
                <?php foreach ($languages as $value => $name) { ?>
                    <option value="<?php echo $value; ?>" <?php echo $value == $language ? 'selected="selected"' : '' ?>><?php echo $name; ?></option>
                <?php } ?>
            </select>
        </label>
    </div>
    <div>
        <label>
            <textarea name="source" cols="80" rows="10"><?php echo $source; ?></textarea>
        </label>
    </div>
    <div>
        <button>Hyphenate</button>
    </div>
</form>
<hr/>
<div class="example">
    <h2>Source</h2>
    <h5>Without hyphens</h5>
    <?php
    echo nl2br($source);
    ?>
</div>


<div class="example">
    <h2>Hyphens</h2>
    <h5>All hyphen locations</h5>
    <?php
    echo implode(" ", $syllable_combine_words);
    ?>
</div>