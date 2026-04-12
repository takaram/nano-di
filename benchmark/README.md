# nano-di benchmark

`benchmark/` は `takaram/nano-di` 本体と切り離した Composer プロジェクトです。  
ベンチマーク実行には `phpbench` を使います。

比較対象:

- `takaram/nano-di`
- `php-di/php-di`
- `league/container`

## 前提

このベンチマークは「コンテナ構築 + ルートオブジェクト 1 回解決」のコールドパスを測ります。  
各ベンチ subject は `benchmarks/ContainerComparisonBench.php` にあります。

## セットアップ

```bash
cd benchmark
composer install
```

## 実行

```bash
composer bench
composer bench:quick
composer bench:full
```

直接 `phpbench` を叩くこともできます。

```bash
XDEBUG_MODE=off vendor/bin/phpbench run --report=aggregate
XDEBUG_MODE=off vendor/bin/phpbench run --report=aggregate --filter='bench(PhpDi|NanoDi)'
```

既定の設定は `phpbench.json` にあります。  
反復数や `revs` は CLI オプションで上書きできます。

## 補足

- 比較対象の依存グラフは `src/Fixture/` にあります。
- `ContainerFactory` は各コンテナの構築方法だけを担当します。
- 将来的に PHP-DI の compile 有効版などを増やしたければ、bench subject を追加すれば比較できます。
