<?php

namespace App\Console\Commands;

use App\Exports\SeoPageExport;
use Illuminate\Console\Command;

use Embed\Embed;

class ExportPages extends Command
{
    protected $signature = 'seo:export {sitemap}';

    protected $description = 'Export all pages from sitemap.xml to excel file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // ToDo -> add  asynchronously https://github.com/oscarotero/Embed#parallel-multiple-requests
        $urls = $this->parseSitemapXml($this->argument('sitemap'));
        $this->info(count($urls). ' total urls');

        $embed = new Embed();

        $pages = [];

        $bar = $this->output->createProgressBar(count($urls));

        $infos = $embed->getMulti( ...$urls );

        foreach($infos as $info)
        {
            // dd($info);
            // $info = $embed->get($url);
            $meta = $info->getMetas();
            $document = $info->getDocument();

            $pages[] = [
                'url_short'=> $this->shortUrl((string)$info->url),
                'title' => $info->title,
                'description' => $info->description,
                'url' => (string)$info->url,
                'canonical' => (string)$info->providerUrl,
                'image' => (string)$info->image,
                'og:title'=> $meta->str('og:title'),
                'og:image'=> $meta->str('og:image'),
                'og:url'=> $meta->str('og:url'),
                'og:site_name'=> $meta->str('og:site_name'),
                'og:description'=> $meta->str('og:description'),
                'og:type'=> $meta->str('og:type'),
                'h1'=> $document->select('.//h1')->str(),
                'h2'=> $document->select('.//h2')->strAll(),
            ];
            $bar->advance();
        }

        $bar->finish();

        $this->info('Finished scraping meta data');

        $export = new SeoPageExport($pages);

        $domain = str_ireplace('www.', '', parse_url( $this->argument('sitemap'), PHP_URL_HOST));
        $filename = $domain.'--'.date("Y-m-d").'.xlsx';

        $this->info('File saved at: /storage/app/'.$filename);

        return \Excel::store($export, $filename);
    }

    public function parseSitemapXml($sitemap)
    {
        try{
            $content = file_get_contents($sitemap);
        }
        catch(\Exception $e){
            $this->error($sitemap . ' does not exist');
            die();
        }

        $this->info('parsing sitemap: '.$sitemap);


        try{
            $xml = simplexml_load_string($content);
        }
        catch(\Exception $e){
            $this->error($sitemap . ' is not valid XML');
            die();
        }

        $urls = [];

        foreach ($xml->url as $urlElement) {
            $url = $urlElement->loc;
            $urls[] = $url;
        }

        return $urls;
    }

    public function shortUrl($url)
    {
        return parse_url($url, PHP_URL_PATH);
    }

}
