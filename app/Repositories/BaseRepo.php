<?php
declare (strict_types = 1);

namespace App\Repositories;

use Hyperf\Di\Annotation\Inject;

class BaseRepo
{
	public function paginator($oQuery, $iPage = 1, $iSize = 20)
	{
		$aResult = [];
		$iTotalCount = $oQuery->count();
		$aResult['list'] = $oQuery->forPage($iPage, $iSize)->get();
		$aResult['page_info'] = [
			'now_page' => (int) $iPage,
		    'pageCount' => (int) ceil($iTotalCount / $iSize),
		    'size' => (int) $iSize,
		    'total_count' => (int) $iTotalCount
		];
		return $aResult;
	}

}
