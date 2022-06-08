<?php
namespace App\Repositories;

use App\Utils\FriendlyException;
use App\Utils\FriendlyResponse;

class BaseRepository
{
    protected $rules = [];

    public function validate($data, $rules)
    {
        $validation = validator($data, $rules);

        if($validation->fails()) {
            throw new FriendlyException('Validation failed', FriendlyResponse::STATUS_CODE_UNPROCESSABLE_ENTITY, $validation->errors());
        }

        return $validation->validate();
    }

    public function transform( array$data, ?string $keys=null ): array|string
    {
        $transformed = [];
        if( $keys ){
            $rules = $this->rules;
            foreach (explode('.', $keys) as $key) {
                $rules = $rules[$key] ?? [];
            }
            $transform = $rules['transform'] ?? [];
            unset($rules);
            foreach ($transform as $key => $value) {
                if (isset($data[$value])) {
                    $transformed[$key] = $data[$value];
                }
            }
            unset($transform);
        }
        return $transformed;
    }

    public function getRulesChild($rules, $key)
    {
        $explode = [
            'base' => [],
            'target' => [],
            'child_rules' => [],
            'target_key' => $key,
        ];
        foreach ($rules as $k => $v) {
            if (str_starts_with($k, $key) && $k !== $key) {
                $x = explode('.*.', $k);
                array_shift($x);
                $y = explode('.*.', $v);
                $explode['key'] = array_shift($y);
                $explode['target'][] = implode('.*.', $x);
                $explode['base'][] = implode('.*.', $y);
                $explode['child_rules'][implode('.*.', $y)] = implode('.*.', $x);
            }
        }
        return $explode;
    }

}
