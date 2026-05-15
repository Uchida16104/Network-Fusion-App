using System;
using System.Collections.Generic;
using System.Linq;

public static class NetworkInspector
{
    public static string Summary(IEnumerable<string> nodes)
    {
        return $"Nodes: {string.Join(", ", nodes)}";
    }
}
