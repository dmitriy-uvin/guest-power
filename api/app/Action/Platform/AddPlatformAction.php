<?php

declare(strict_types=1);

namespace App\Action\Platform;

use App\Models\Ahrefs;
use App\Models\Alexa;
use App\Models\Majestic;
use App\Models\Moz;
use App\Models\Platform;
use App\Models\SemRush;
use App\Models\Topic;
use App\Repositories\Platform\PlatformRepositoryInterface;

final class AddPlatformAction
{
    private PlatformRepositoryInterface $platformRepository;

    public function __construct(PlatformRepositoryInterface $platformRepository)
    {
        $this->platformRepository = $platformRepository;
    }

    public function execute(AddPlatformRequest $request)
    {
        $platform = new Platform();
        $platform->website_url = $request->getWebsiteUrl();
        $platform->dr = $request->getDr();
        $platform->ma = $request->getMa();
        $platform->organic_traffic = $request->getOrganicTraffic();
        $platform->dofollow_active = $request->getDoFollowActive();
        $platform->free_home_featured_active = $request->getFreeHomeFeaturedActive();
        $platform->niche_edit_link_active = $request->getNicheEditLinkActive();
        if ($request->getNicheEditLinkActive()) {
            $platform->niche_edit_link_price = $request->getNicheEditLinkPrice();
        }
        $platform->article_writing_price = $request->getArticleWritingPrice();
        $platform->article_requirements = $request->getArticleRequirements();
        $platform->deadline = $request->getDeadLine();
        $platform->where_posted = $request->getWherePosted();
        $platform->contacts = $request->getContacts();
        $platform->comment = $request->getComment();
        $platform->price = $request->getPrice();
        $platform->description = $request->getDescription();
        $platform->email = $request->getEmail();
        $platform->domain_zone = $this->getDomainZone($request->getWebsiteUrl());
        $platform = $this->platformRepository->save($platform);

        if ($request->getTopics()) {
            $this->platformRepository->saveTopics($platform, $request->getTopics());
        }

        $moz = new Moz([
            'platform_id' => $platform->id,
            'pa' => $request->getMozPA(),
            'da' => $request->getMozDA(),
            'rank' => $request->getMozRank(),
            'links_in' => $request->getMozLinksIn(),
            'equity' => $request->getMozEquity()
        ]);
        $moz->save();

        $alexa = new Alexa([
            'platform_id' => $platform->id,
            'rank' => $request->getAlexaRank(),
            'country' => $request->getAlexaCountry(),
            'country_rank' => $request->getAlexaCountryRank()
        ]);
        $alexa->save();

        $semrush = new SemRush([
            'platform_id' => $platform->id,
            'rank' => $request->getSemrushRank(),
            'keyword_num' => $request->getSemrushKeywordNum(),
            'traffic' => $request->getSemrushTraffic(),
            'traffic_costs' => $request->getSemrushTrafficCosts(),
        ]);
        $semrush->save();

        $majestic = new Majestic([
            'platform_id' => $platform->id,
            'external_backlinks' => $request->getMajesticExternalBacklinks(),
            'external_gov' => $request->getMajesticExternalGov(),
            'external_edu' => $request->getMajesticExternalEdu(),
            'tf' => $request->getMajesticTF(),
            'cf' => $request->getMajesticCF(),
        ]);
        $majestic->save();

        if ($request->getAhrefsStatus()) {
            $ahrefs = new Ahrefs([
                'platform_id' => $platform->id,
                'rank' => $request->getAhrefsRank(),
                'dr' => $request->getAhrefsDr(),
                'eb' => $request->getAhrefsEb(),
                'rd' => $request->getAhrefsRd(),
                'dofollow' => $request->getAhrefsDofollow(),
                'ips' => $request->getAhrefsIps(),
            ]);
            $ahrefs->save();
        }

        return new AddPlatformResponse($platform);
    }

    private function getDomainZone(string $websiteUrl): string
    {
        $string = trim($websiteUrl, '/');
        $string = mb_ereg_replace("http[s]?:[\/]{2}", '', $string);
        $domain = explode('/', $string)[0];
        $domainParts = explode('.', $domain);

        if (isset($domainParts[count($domainParts) - 1])) {
            return $domainParts[count($domainParts) - 1];
        }
    }
}
