<?php

namespace App\Services;

use Carbon\Carbon;

class NetworkAnalyzer
{
    public function analyze(array $nodes, array $edges, ?string $focus = null): array
    {
        $graph = $this->buildGraph($nodes, $edges);
        $components = $this->connectedComponents($graph);
        $distances = $focus && isset($graph[$focus]) ? $this->bfs($graph, $focus) : [];
        $isolated = array_values(array_filter(array_keys($graph), fn ($node) => count($graph[$node]) === 0));

        return [
            'summary' => [
                'node_count' => count($nodes),
                'edge_count' => count($edges),
                'component_count' => count($components),
                'isolated_nodes' => $isolated,
                'focus' => $focus,
                'generated_at' => Carbon::now()->toIso8601String(),
            ],
            'components' => $components,
            'distances_from_focus' => $distances,
            'adjacency' => $graph,
            'recommendations' => $this->recommendations($graph, $components, $focus),
        ];
    }

    public function repair(array $nodes, array $edges, ?string $focus = null): array
    {
        $graph = $this->buildGraph($nodes, $edges);
        $components = $this->connectedComponents($graph);

        if (count($components) <= 1) {
            return [
                'status' => 'already_connected',
                'repairs' => [],
                'message' => 'The graph is already connected.',
            ];
        }

        $repairs = [];
        $anchor = $focus && isset($graph[$focus]) ? $focus : $components[0][0];
        $anchorComponent = $this->componentContaining($components, $anchor);

        foreach ($components as $component) {
            if ($component === $anchorComponent) {
                continue;
            }
            $target = $component[0];
            $repairs[] = [
                'action' => 'add_edge',
                'from' => $anchor,
                'to' => $target,
                'reason' => 'Connect disconnected component to the primary component.',
            ];
        }

        return [
            'status' => 'repair_plan_ready',
            'anchor' => $anchor,
            'repairs' => $repairs,
            'algorithm' => [
                'name' => 'component-bridging',
                'steps' => [
                    'Build adjacency list',
                    'Detect connected components',
                    'Bridge every non-anchor component',
                    'Re-run connectivity check',
                ],
            ],
        ];
    }

    private function buildGraph(array $nodes, array $edges): array
    {
        $graph = [];
        foreach ($nodes as $node) {
            $graph[(string)$node] = [];
        }

        foreach ($edges as $edge) {
            [$a, $b] = [$edge[0], $edge[1]];
            $graph[$a] = $graph[$a] ?? [];
            $graph[$b] = $graph[$b] ?? [];
            if (!in_array($b, $graph[$a], true)) {
                $graph[$a][] = $b;
            }
            if (!in_array($a, $graph[$b], true)) {
                $graph[$b][] = $a;
            }
        }

        return $graph;
    }

    private function connectedComponents(array $graph): array
    {
        $visited = [];
        $components = [];

        foreach (array_keys($graph) as $start) {
            if (isset($visited[$start])) {
                continue;
            }

            $queue = [$start];
            $component = [];
            $visited[$start] = true;

            while ($queue) {
                $node = array_shift($queue);
                $component[] = $node;

                foreach ($graph[$node] as $neighbor) {
                    if (!isset($visited[$neighbor])) {
                        $visited[$neighbor] = true;
                        $queue[] = $neighbor;
                    }
                }
            }

            $components[] = $component;
        }

        return $components;
    }

    private function bfs(array $graph, string $start): array
    {
        $distance = [$start => 0];
        $queue = [$start];

        while ($queue) {
            $node = array_shift($queue);

            foreach ($graph[$node] as $neighbor) {
                if (!array_key_exists($neighbor, $distance)) {
                    $distance[$neighbor] = $distance[$node] + 1;
                    $queue[] = $neighbor;
                }
            }
        }

        return $distance;
    }

    private function recommendations(array $graph, array $components, ?string $focus): array
    {
        $recs = [];
        if (count($components) > 1) {
            $recs[] = 'Bridge disconnected components with one or more edges.';
        }
        if ($focus && isset($graph[$focus]) && count($graph[$focus]) === 0) {
            $recs[] = "Focus node '{$focus}' is isolated and should be linked first.";
        }
        if (!$recs) {
            $recs[] = 'Topology is structurally healthy.';
        }
        return $recs;
    }

    private function componentContaining(array $components, string $node): array
    {
        foreach ($components as $component) {
            if (in_array($node, $component, true)) {
                return $component;
            }
        }
        return $components[0] ?? [];
    }
}
