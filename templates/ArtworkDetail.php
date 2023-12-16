<?
$artwork = $artwork ?? null;
$showPDProofTip = $showPDProofTip ?? true;
?>

<h1><?= Formatter::ToPlainText($artwork->Name) ?></h1>
<a href="<?= $artwork->ImageUrl ?>">
	<picture>
		<img src="<?= $artwork->ThumbUrl ?>" property="schema:image"/>
	</picture>
</a>
<h2>Metadata</h2>
<table class="artwork-metadata">
	<tr>
		<td>Title</td>
		<td><?= Formatter::ToPlainText($artwork->Name) ?></td>
	</tr>
	<tr>
		<td>Artist</td>
		<td>
			<?= Formatter::ToPlainText($artwork->Artist->Name) ?>
			<? if(sizeof($artwork->Artist->AlternateSpellings) > 0){ ?>(<abbr>AKA</abbr> <span class="author" typeof="schema:Person" property="schema:name"><?= implode('</span>, <span class="author" typeof="schema:Person" property="schema:name">', array_map('Formatter::ToPlainText', $artwork->Artist->AlternateSpellings)) ?></span>)<? } ?>
			<? if($artwork->Artist->DeathYear !== null){ ?>, <abbr title="deceased">d.</abbr> <?= $artwork->Artist->DeathYear ?><? } ?>
		</td>
	</tr>
	<tr>
		<td>Year completed</td>
		<td><? if($artwork->CompletedYear === null){ ?>(unknown)<? }else{ ?><?= $artwork->CompletedYear ?><? if($artwork->CompletedYearIsCirca){ ?> (circa)<? } ?><? } ?></td>
	</tr>
	<tr>
		<td>File size</td>
		<td><?= $artwork->ImageSize ?></td>
	</tr>
	<tr>
		<td>Uploaded</td>
		<td><?= $artwork->Created->format('F j, Y g:i a') ?></td>
	</tr>
	<tr>
		<td>Status</td>
		<td><?= Template::ArtworkStatus(['artwork' => $artwork]) ?></td>
	</tr>
	<tr>
		<td>Tags</td>
		<td><ul class="tags"><? foreach($artwork->ArtworkTags as $tag){ ?><li><a href="<?= $tag->Url ?>"><?= Formatter::ToPlainText($tag->Name) ?></a></li><? } ?></ul></td>
	</tr>
</table>
<h2>PD Proof</h2>
<? if($showPDProofTip){ ?>
	<aside class="tip">
		<p>PD proof must take the form of:</p>
		<ul>
			<li>Link to an <a href="/manual/latest/10-art-and-images#10.3.3.7.4">approved museum page</a></li>
		</ul>
		<p>or <strong>all</strong> of the following:</p>
		<ol>
			<li>Year book was published</li>
			<li>Link to direct page scan of artwork (not just the start of the book, the direct page)</li>
			<li>Link to direct page scan of page mentioning book publication year (not just the start of the book, the direct page)</li>
			<li>Link to direct page scan of book copyright/rights statement page (may be the same as the publication year page but not always)</li>
		</ol>
	</aside>
<? } ?>
<h3>Museum page</h3>
<ul>
	<li>Link to an approved museum page: <? if($artwork->MuseumUrl !== null){ ?> <a href="<?= Formatter::ToPlainText($artwork->MuseumUrl) ?>">Link</a><? }else{ ?>(not provided)<? } ?></li>
</ul>
<? if(!empty($artwork->MuseumUrl)){ ?>
<? $matchingMuseum = Museum::FindMatch($artwork->MuseumUrl); ?>
<? if($matchingMuseum !== NULL){ ?>
<figure class="corrected full">
	<p>Approved museum: <?= Formatter::ToPlainText($matchingMuseum->Name) ?> <code>(<?= Formatter::ToPlainText($matchingMuseum->Domain) ?>)</code></p>
</figure>
<? }else{ ?>
<figure class="wrong full">
	<p>Not an approved museum</p>
</figure>
<? } ?>
<? } ?>
<h3>Links to scans</h3>
<ol>
	<li>Year book was published: <? if($artwork->PublicationYear !== null){ ?><?= $artwork->PublicationYear ?><? }else{ ?>(not provided)<? } ?></li>
	<li>Link to direct page scan of artwork: <? if($artwork->ArtworkPageUrl !== null){ ?><a href="<?= Formatter::ToPlainText($artwork->ArtworkPageUrl) ?>">Link</a><? }else{ ?>(not provided)<? } ?></li>
	<li>Link to direct page scan of page mentioning book publication year: <? if($artwork->PublicationYearPageUrl !== null){ ?><a href="<?= Formatter::ToPlainText($artwork->PublicationYearPageUrl) ?>">Link</a><? }else{ ?>(not provided)<? } ?></li>
	<li>Link to direct page scan of book copyright/rights statement page: <? if($artwork->CopyrightPageUrl !== null){ ?><a href="<?= Formatter::ToPlainText($artwork->CopyrightPageUrl) ?>">Link</a><? }else{ ?>(not provided)<? } ?></li>
</ol>