<?php function EditorXMLCodeComponent ($props = null) { 
    if ($props === null) $props = [];
    // $props['links'] [('href', 'target', 'label')]
    // $props['title'] 
    // $props['value'] 
    // $props['name'] 
    // $props['id'] 
?>

    <div class="codedit col-12 mb-5">
        <div class="d-flex flex-column py-2">

            <h5 class="fs-5"><?=@$props['title']?></h5>

            <?php if (count(@$props['links'])) : ?>
                <div class="d-flex flex-row">
                    <?php foreach (@$props['links'] as $item) : 
                            $href   = @$item['href']   ? 'href="'.$item['href'].'"' : "";
                            $target = @$item['target'] ? 'target="'.$item['target'].'"' : "";
                            $label  = @$item['label']  ?: "";
                        ?>
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                            <a <?=$href?> <?=$target?> style="font-size: 12px;"><?=$label?></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
        <div class="editor-holder border rounded">
            <div class="scroller position-relative h-100">
                <textarea class="editor allow-tabs" name="<?=@$props['name']?>" id="<?=@$props['id']?>" spellcheck="false"><?=@$props['value']?></textarea>
                <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                    <code class="xml syntax-highight language-xml w-auto"></code>
                </pre>
            </div>
        </div>
    </div>

<?php } ?>