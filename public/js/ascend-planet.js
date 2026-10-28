import * as THREE from 'three';
import { EffectComposer } from 'three/examples/jsm/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/examples/jsm/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/examples/jsm/postprocessing/UnrealBloomPass.js';
import { ShaderPass } from 'three/examples/jsm/postprocessing/ShaderPass.js';
import { GammaCorrectionShader } from 'three/examples/jsm/shaders/GammaCorrectionShader.js';
import { CopyShader } from 'three/examples/jsm/shaders/CopyShader.js';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import { DRACOLoader } from 'three/examples/jsm/loaders/DRACOLoader.js';

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Year helper
const yearEl = document.getElementById('year');
if (yearEl) yearEl.textContent = new Date().getFullYear();

// Lenis smooth scroll (loaded non-blocking)
if (!reducedMotion) {
  import('https://unpkg.com/lenis@1.3.23/dist/lenis.mjs')
    .then(({ default: Lenis }) => {
      const lenis = new Lenis({ duration: 1.15, smoothWheel: true, touchMultiplier: 1.5 });
      function raf(t) {
        lenis.raf(t);
        requestAnimationFrame(raf);
      }
      requestAnimationFrame(raf);
    })
    .catch(() => {});
}

// Staggered scroll reveal
const revealEls = document.querySelectorAll('[data-reveal]');
if (reducedMotion) {
  revealEls.forEach(el => el.classList.add('in'));
} else {
  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('in');
        io.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -4% 0px' });
  revealEls.forEach(el => io.observe(el));
}

// WebGL Planet Scene
const canvas = document.querySelector('.planet-canvas');
if (canvas) {
  const ASSET_BASE_URL = '/assets';
  const PLANET_GLB = `${ASSET_BASE_URL}/planet.glb`;
  const PLANET_LIGHTS_GLB = `${ASSET_BASE_URL}/planet-lights.glb`;
  const PLANET_CLOUDS_PNG = `${ASSET_BASE_URL}/planet-clouds.png`;

  const CONFIG = {
    rimColor:'#93c5fd', rimPower:2.8, nightLights:8, terrainDepth:0.33, terrainShade:1.3,
    oceanGlint:0.35, oceanDeep:0.10, oceanFlow:2.5, oceanFlowSpeed:0.6, oceanFlowScale:2.1,
    glowColor:'#1d4ed8', glowIntensity:1.15, planetRadius:1.95, spin:0.025, initRotation:2.07,
    tilt:0.37, autoRotate:0,
    cloud1Height:1.005, cloud1Opacity:0.45, cloud1Spin:0.05,
    cloud2Height:1.03, cloud2Opacity:0.38, cloud2Spin:0.11,
    cloud3Height:1.075, cloud3Opacity:0.38, cloud3Spin:0.08,
    bgColor:'#090b10', flameColor:'#2563eb', flameColor2:'#93c5fd', flameAmt:0.08,
    atmoColor:'#60a5fa', atmoCount:220, atmoSize:18, atmoSpeed:0.6,
    starColor:'#cbd5e1', starCount:950, starSize:1.2, starFlicker:0.8,
    markerColor:'#38bdf8', markerCount:40, markerSize:14, markerSpeed:0.4,
  };
  const LAYERS = { NONE:0, TORUS_SCENE:1, BLOOM_SCENE:2, ENTIRE_SCENE:3 };

  const Lerp = (a, b, t) => a + (b - a) * t;
  const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));
  const hexToVec3 = (hex) => {
    const c = new THREE.Color(hex);
    return new THREE.Vector3(c.r, c.g, c.b);
  };
  const sample = (stops, p) => {
    if (p <= stops[0].p) return stops[0].v;
    for (let i = 0; i < stops.length - 1; i++) {
      const a = stops[i], b = stops[i + 1];
      if (p <= b.p) {
        const t = clamp((p - a.p) / (b.p - a.p), 0, 1);
        const s = t * t * (3 - 2 * t);
        return Lerp(a.v, b.v, s);
      }
    }
    return stops[stops.length - 1].v;
  };

  const SNOISE = `
vec4 permute(vec4 x){return mod(((x*34.0)+1.0)*x, 289.0);}
vec4 taylorInvSqrt(vec4 r){return 1.79284291400159 - 0.85373472095314 * r;}
float snoise(vec3 v){
  const vec2 C = vec2(1.0/6.0, 1.0/3.0); const vec4 D = vec4(0.0, 0.5, 1.0, 2.0);
  vec3 i = floor(v + dot(v, C.yyy)); vec3 x0 = v - i + dot(i, C.xxx);
  vec3 g = step(x0.yzx, x0.xyz); vec3 l = 1.0 - g;
  vec3 i1 = min(g.xyz, l.zxy); vec3 i2 = max(g.xyz, l.zxy);
  vec3 x1 = x0 - i1 + 1.0 * C.xxx; vec3 x2 = x0 - i2 + 2.0 * C.xxx; vec3 x3 = x0 - 1.0 + 3.0 * C.xxx;
  i = mod(i, 289.0);
  vec4 p = permute(permute(permute(i.z + vec4(0.0, i1.z, i2.z, 1.0)) + i.y + vec4(0.0, i1.y, i2.y, 1.0)) + i.x + vec4(0.0, i1.x, i2.x, 1.0));
  float n_ = 1.0/7.0; vec3 ns = n_ * D.wyz - D.xzx;
  vec4 j = p - 49.0 * floor(p * ns.z *ns.z);
  vec4 x_ = floor(j * ns.z); vec4 y_ = floor(j - 7.0 * x_);
  vec4 x = x_ *ns.x + ns.yyyy; vec4 y = y_ *ns.x + ns.yyyy; vec4 h = 1.0 - abs(x) - abs(y);
  vec4 b0 = vec4(x.xy, y.xy); vec4 b1 = vec4(x.zw, y.zw);
  vec4 s0 = floor(b0)*2.0 + 1.0; vec4 s1 = floor(b1)*2.0 + 1.0; vec4 sh = -step(h, vec4(0.0));
  vec4 a0 = b0.xzyw + s0.xzyw*sh.xxyy; vec4 a1 = b1.xzyw + s1.xzyw*sh.zzww;
  vec3 p0 = vec3(a0.xy,h.x); vec3 p1 = vec3(a0.zw,h.y); vec3 p2 = vec3(a1.xy,h.z); vec3 p3 = vec3(a1.zw,h.w);
  vec4 norm = taylorInvSqrt(vec4(dot(p0,p0), dot(p1,p1), dot(p2, p2), dot(p3,p3)));
  p0 *= norm.x; p1 *= norm.y; p2 *= norm.z; p3 *= norm.w;
  vec4 m = max(0.5 - vec4(dot(x0,x0), dot(x1,x1), dot(x2,x2), dot(x3,x3)), 0.0); m = m * m;
  return 42.0 * dot(m*m, vec4(dot(p0,x0), dot(p1,x1), dot(p2,x2), dot(p3,x3)));
}
`;

  let w = window.innerWidth, h = window.innerHeight;
  const renderer = new THREE.WebGL1Renderer({ canvas, antialias: true });
  renderer.setPixelRatio(window.devicePixelRatio);
  renderer.setSize(w, h);
  renderer.outputEncoding = THREE.sRGBEncoding;
  renderer.shadowMap.enabled = true;
  renderer.shadowMap.type = THREE.VSMShadowMap;

  const scene = new THREE.Scene();
  scene.background = new THREE.Color(0x000000);

  const camera = new THREE.PerspectiveCamera(40, w / h, 0.1, 200);
  camera.position.set(0, 0, 8);
  camera.layers.enable(LAYERS.TORUS_SCENE);
  camera.layers.enable(LAYERS.BLOOM_SCENE);
  camera.layers.enable(LAYERS.ENTIRE_SCENE);
  scene.add(camera);

  const ambLight = new THREE.AmbientLight(0xffffff, 1.8);
  ambLight.layers.set(LAYERS.ENTIRE_SCENE);
  scene.add(ambLight);

  const dirLight = new THREE.DirectionalLight(0xffffff, 0.8);
  dirLight.position.set(0, 10, 2);
  dirLight.layers.set(LAYERS.ENTIRE_SCENE);
  scene.add(dirLight);

  const torusComposer = new EffectComposer(renderer);
  torusComposer.renderToScreen = false;
  torusComposer.addPass(new RenderPass(scene, camera));
  torusComposer.addPass(new ShaderPass(GammaCorrectionShader));
  torusComposer.addPass(new UnrealBloomPass(new THREE.Vector2(w, h), 0.22, 0.2, 0));
  torusComposer.addPass(new ShaderPass(CopyShader));

  const bloomComposer = new EffectComposer(renderer);
  bloomComposer.renderToScreen = false;
  bloomComposer.addPass(new RenderPass(scene, camera));
  bloomComposer.addPass(new UnrealBloomPass(new THREE.Vector2(w, h), 0.5, 0.6, 0));
  bloomComposer.addPass(new ShaderPass(GammaCorrectionShader));

  const FinalPass = {
    uniforms: {
      iTime:{value:0}, tDiffuse:{value:null}, torusTexture:{value:torusComposer.renderTarget1.texture},
      bloomTexture:{value:bloomComposer.renderTarget1.texture}, haloTexture:{value:null},
      uBg:{value:hexToVec3(CONFIG.bgColor)}, uFlameA:{value:hexToVec3(CONFIG.flameColor)},
      uFlameB:{value:hexToVec3(CONFIG.flameColor2)}, uFlameAmt:{value:CONFIG.flameAmt},
    },
    vertexShader: `varying vec2 vUv; void main(){ vUv = uv; gl_Position = vec4(position, 1.0); }`,
    fragmentShader: `
      uniform float iTime; uniform sampler2D tDiffuse; uniform sampler2D bloomTexture; uniform sampler2D torusTexture; uniform sampler2D haloTexture;
      uniform vec3 uBg; uniform vec3 uFlameA; uniform vec3 uFlameB; uniform float uFlameAmt;
      varying vec2 vUv;
      vec3 warp3d(vec3 pos, float t){ float curv=.8,a=1.9,b=0.7; pos*=2.;
        pos.x+=curv*sin(t+a*pos.y)+t*b; pos.y+=curv*cos(t+a*pos.x);
        pos.y+=curv*sin(t+a*pos.z)+t*b; pos.z+=curv*cos(t+a*pos.y);
        pos.z+=curv*sin(t+a*pos.x)+t*b; pos.x+=curv*cos(t+a*pos.z);
        return 0.5+0.5*cos(pos.xyz+vec3(1,2,4)); }
      void main(){
        vec2 uv = 2.*vUv - 1.;
        vec3 w = pow(warp3d(vec3(uv.x, sin(uv.y), uv.y), iTime*1.5), vec3(1.5));
        vec3 flame = 1.5*uFlameA*w.x; flame*=w.y; flame += uFlameB*w.z;
        flame *= smoothstep(0.25, 1., abs(uv.y));
        float md = smoothstep(-0.7, 1., -uv.y*uv.x); flame *= md*md;
        vec3 bg = uBg * (1.0 - 0.4 * length(uv));
        vec3 halo = texture2D(haloTexture, vUv).xyz;
        gl_FragColor = vec4(bg + flame*uFlameAmt + texture2D(bloomTexture, vUv).xyz + texture2D(torusTexture, vUv).xyz + texture2D(tDiffuse, vUv).xyz + halo, 1.);
      }`,
  };

  const finalPass = new ShaderPass(FinalPass);
  const finalComposer = new EffectComposer(renderer);
  finalComposer.addPass(new RenderPass(scene, camera));
  finalComposer.addPass(finalPass);

  const STOPS_X = [{p:0,v:0},{p:0.32,v:-3.1},{p:0.64,v:3.2},{p:1,v:0}];
  const STOPS_Y = [{p:0,v:-4.5},{p:0.32,v:0.55},{p:0.64,v:0.45},{p:1,v:0.15}];
  const STOPS_S = [{p:0,v:2.15},{p:0.32,v:1.0},{p:0.64,v:0.92},{p:1,v:1.12}];

  const worldGroup = new THREE.Group();
  worldGroup.position.set(STOPS_X[0].v, STOPS_Y[0].v, 0);
  worldGroup.scale.setScalar(STOPS_S[0].v);
  scene.add(worldGroup);

  const planetGroup = new THREE.Group();
  planetGroup.rotation.z = CONFIG.tilt;
  worldGroup.add(planetGroup);

  const cloudGroup = new THREE.Group();
  cloudGroup.rotation.z = CONFIG.tilt;
  cloudGroup.visible = false;
  worldGroup.add(cloudGroup);

  // Atmosphere halo glow
  const glowMat = new THREE.ShaderMaterial({
    transparent: true,
    side: THREE.DoubleSide,
    depthWrite: false,
    blending: THREE.AdditiveBlending,
    uniforms: {
      uGlow: { value: hexToVec3(CONFIG.glowColor) },
      uIntensity: { value: CONFIG.glowIntensity },
    },
    vertexShader: `varying vec2 vUv; void main(){ vUv=uv; gl_Position = projectionMatrix*modelViewMatrix*vec4(position,1.0); }`,
    fragmentShader: `
      uniform vec3 uGlow; uniform float uIntensity; varying vec2 vUv;
      void main(){
        float d = length(vUv - 0.5) * 2.0;
        float a = pow(clamp(1.0 - d, 0.0, 1.0), 2.2);
        gl_FragColor = vec4(uGlow * a * uIntensity, a);
      }`,
  });
  const glowMesh = new THREE.Mesh(new THREE.PlaneGeometry(2, 2), glowMat);
  glowMesh.scale.setScalar(CONFIG.planetRadius * 2.3);
  glowMesh.layers.set(LAYERS.ENTIRE_SCENE);
  worldGroup.add(glowMesh);

  // Shared time uniforms
  const planetTimeUniform = { value: 0 };
  const cloudTimeUniform = { value: 0 };

  // Cloud shells (3 layers)
  const cloudMeshes = [];
  const texLoader = new THREE.TextureLoader();
  texLoader.setCrossOrigin('anonymous');
  texLoader.load(PLANET_CLOUDS_PNG, (cloudTex) => {
    cloudTex.wrapS = THREE.RepeatWrapping;
    cloudTex.wrapT = THREE.RepeatWrapping;
    cloudTex.repeat.set(5, 5);
    const cloudConfigs = [
      { h: 'cloud1Height', o: 'cloud1Opacity', s: 'cloud1Spin', ry: 0.0, phase: 0.0 },
      { h: 'cloud2Height', o: 'cloud2Opacity', s: 'cloud2Spin', ry: 2.2, phase: 13.0 },
      { h: 'cloud3Height', o: 'cloud3Opacity', s: 'cloud3Spin', ry: 4.3, phase: 27.0 },
    ];
    cloudConfigs.forEach((layer) => {
      const geo = new THREE.SphereGeometry(CONFIG.planetRadius * CONFIG[layer.h], 64, 64);
      const mat = new THREE.MeshStandardMaterial({ map: cloudTex, transparent: true, depthWrite: false });
      mat.onBeforeCompile = (shader) => {
        shader.uniforms.uTime = cloudTimeUniform;
        shader.uniforms.noiseScale = { value: 20.0 };
        shader.uniforms.uSpeedX = { value: 1.0 };
        shader.uniforms.uSpeedY = { value: 2.0 };
        shader.uniforms.uSpeedZ = { value: 2.0 };
        shader.uniforms.uOpacity = { value: CONFIG[layer.o] };
        shader.uniforms.uPhase = { value: layer.phase };
        shader.vertexShader = `varying vec2 vCloudUv;\n` + shader.vertexShader.replace(
          `void main() {`,
          `void main() {\n vCloudUv = uv;`
        );
        shader.fragmentShader = `
          uniform float uTime; uniform float noiseScale; uniform float uSpeedX; uniform float uSpeedY; uniform float uSpeedZ;
          uniform float uOpacity; uniform float uPhase; varying vec2 vCloudUv;
          ${SNOISE}
        ` + shader.fragmentShader.replace(
          `#include <dithering_fragment>`,
          `#include <dithering_fragment>
          gl_FragColor.rgb = vec3(1.0);
          float cloudNoise = snoise(vec3(vCloudUv.x * noiseScale + uTime * uSpeedX + uPhase, vCloudUv.y * noiseScale - uTime * uSpeedY + uPhase, uTime * uSpeedZ + uPhase));
          float cloudNdv = max(dot(normalize(vNormal), normalize(vViewPosition)), 0.0);
          float cloudEdge = pow(1.0 - cloudNdv, 3.0);
          float cloudMod = mix(cloudNoise, 1.0, cloudEdge);
          float cloudNdl = dot(normalize(vNormal), normalize(vec3(-0.9, 0.18, 0.4)));
          float cloudDay = 1.0 - smoothstep(0.30, -0.30, cloudNdl) * 0.9;
          gl_FragColor.a *= cloudMod * uOpacity * cloudDay;`
        );
      };
      const m = new THREE.Mesh(geo, mat);
      m.rotation.y = layer.ry;
      m.renderOrder = 2;
      m.layers.set(LAYERS.ENTIRE_SCENE);
      cloudGroup.add(m);
      cloudMeshes.push({ mesh: m, spinKey: layer.s });
    });
  });

  // Ambient atmosphere motes
  const atmoPos = new Float32Array(CONFIG.atmoCount * 3);
  const atmoSizes = new Float32Array(CONFIG.atmoCount);
  const atmoSeeds = new Float32Array(CONFIG.atmoCount);
  for (let i = 0; i < CONFIG.atmoCount; i++) {
    atmoPos[i * 3] = Math.random() * 2 - 1;
    atmoPos[i * 3 + 1] = Math.random() * 2 - 1;
    atmoPos[i * 3 + 2] = Math.random() * 2 - 1;
    atmoSizes[i] = CONFIG.atmoSize * (0.4 + Math.random());
    atmoSeeds[i] = Math.random() * 100;
  }
  const atmoGeo = new THREE.BufferGeometry();
  atmoGeo.setAttribute('position', new THREE.BufferAttribute(atmoPos, 3));
  atmoGeo.setAttribute('size', new THREE.BufferAttribute(atmoSizes, 1));
  atmoGeo.setAttribute('seed', new THREE.BufferAttribute(atmoSeeds, 1));
  const atmoMat = new THREE.ShaderMaterial({
    transparent: true,
    depthWrite: false,
    depthTest: false,
    blending: THREE.AdditiveBlending,
    uniforms: {
      uTime: { value: 0 },
      uRes: { value: new THREE.Vector2(w, h) },
      uColor: { value: hexToVec3(CONFIG.atmoColor) },
    },
    vertexShader: `
      attribute float size; attribute float seed; uniform float uTime; uniform vec2 uRes;
      varying float vA;
      vec3 warp(vec3 p, float t){ float c=0.9,a=1.9,b=0.02,s=0.05; p*=2.;
        p.x+=c*sin(s*t+a*p.y)+t*b; p.y+=c*cos(s*t+a*p.x); p.y+=c*sin(s*t+a*p.z)+t*b;
        p.z+=c*cos(s*t+a*p.y); p.z+=c*sin(s*t+a*p.x)+t*b; p.x+=c*cos(s*t+a*p.z);
        return cos(p+vec3(1,2,4)); }
      void main(){
        vec3 v = position*4.0 + warp(position, uTime)*1.2;
        vec4 mv = modelViewMatrix * vec4(v, 1.0);
        float r = length(v); float farF = 1.0 - smoothstep(5.0, 6.5, r); float nearF = smoothstep(0.0, 0.5, -mv.z);
        vA = farF * nearF;
        gl_PointSize = size * uRes.y / 900.0 / -mv.z; gl_PointSize = max(gl_PointSize, 1.0);
        gl_Position = projectionMatrix * mv;
      }`,
    fragmentShader: `
      uniform vec3 uColor; varying float vA;
      void main(){ vec2 p = gl_PointCoord - 0.5; float l = length(p); if (l > 0.5) discard;
        float tex = smoothstep(0.5, 0.0, l); gl_FragColor = vec4(uColor * tex, tex * vA * 0.55); }`,
  });
  const atmoPoints = new THREE.Points(atmoGeo, atmoMat);
  atmoPoints.layers.set(LAYERS.ENTIRE_SCENE);
  atmoPoints.onBeforeRender = () => {
    const perfNow = performance.now() * 0.001;
    atmoMat.uniforms.uTime.value = perfNow * CONFIG.atmoSpeed * 8.0;
    atmoPoints.position.copy(camera.position);
    finalPass.uniforms.iTime.value = perfNow;
  };
  scene.add(atmoPoints);

  // Starfield (r=90)
  const starPos = new Float32Array(CONFIG.starCount * 3);
  const starSeeds = new Float32Array(CONFIG.starCount);
  const starBright = new Float32Array(CONFIG.starCount);
  for (let i = 0; i < CONFIG.starCount; i++) {
    const u = Math.random(), v = Math.random();
    const theta = 2 * Math.PI * u, phi = Math.acos(2 * v - 1);
    const r = 90;
    starPos[i * 3] = r * Math.sin(phi) * Math.cos(theta);
    starPos[i * 3 + 1] = r * Math.sin(phi) * Math.sin(theta);
    starPos[i * 3 + 2] = r * Math.cos(phi);
    starSeeds[i] = Math.random() * 100;
    starBright[i] = 0.35 + Math.random() * 0.65;
  }
  const starGeo = new THREE.BufferGeometry();
  starGeo.setAttribute('position', new THREE.BufferAttribute(starPos, 3));
  starGeo.setAttribute('seed', new THREE.BufferAttribute(starSeeds, 1));
  starGeo.setAttribute('bright', new THREE.BufferAttribute(starBright, 1));
  const starMat = new THREE.ShaderMaterial({
    transparent: true,
    depthWrite: false,
    depthTest: true,
    blending: THREE.AdditiveBlending,
    uniforms: {
      uTime: { value: 0 },
      uSize: { value: CONFIG.starSize },
      uFlicker: { value: CONFIG.starFlicker },
      uColor: { value: hexToVec3(CONFIG.starColor) },
      uRes: { value: new THREE.Vector2(w, h) },
    },
    vertexShader: `
      attribute float seed; attribute float bright;
      uniform float uTime; uniform float uSize; uniform float uFlicker; uniform vec2 uRes;
      varying float vTw;
      void main(){
        vec4 mv = modelViewMatrix * vec4(position, 1.0);
        float tw = 0.6 + 0.4 * sin(uTime * uFlicker + seed);
        vTw = bright * tw;
        gl_PointSize = max(uSize * uRes.y / 900.0 * (90.0 / max(-mv.z, 1.0)), 1.0);
        gl_Position = projectionMatrix * mv;
      }`,
    fragmentShader: `
      uniform vec3 uColor; varying float vTw;
      void main(){
        vec2 p = gl_PointCoord - 0.5; float l = length(p); if (l > 0.5) discard;
        float core = smoothstep(0.5, 0.0, l);
        gl_FragColor = vec4(uColor, core * vTw);
      }`,
  });
  const starPoints = new THREE.Points(starGeo, starMat);
  starPoints.frustumCulled = false;
  starPoints.layers.set(LAYERS.ENTIRE_SCENE);
  scene.add(starPoints);

  // Land markers uniform
  const markerMat = new THREE.ShaderMaterial({
    transparent: true,
    depthTest: true,
    depthWrite: false,
    blending: THREE.AdditiveBlending,
    uniforms: {
      uTime: { value: 0 },
      uColor: { value: hexToVec3(CONFIG.markerColor) },
      uSize: { value: CONFIG.markerSize },
      uSpeed: { value: CONFIG.markerSpeed },
      uRes: { value: new THREE.Vector2(w, h) },
    },
    vertexShader: `
      attribute float seed; uniform float uSize; uniform vec2 uRes;
      varying float vSeed; varying float vFade;
      void main(){
        vSeed = seed;
        vec4 mv = modelViewMatrix * vec4(position, 1.0);
        vec3 vn = normalize(normalMatrix * normalize(position));
        vec3 vd = normalize(-mv.xyz);
        vFade = smoothstep(0.15, 0.5, dot(vn, vd));
        gl_PointSize = max(uSize * uRes.y / 900.0 * (7.0 / max(-mv.z, 1.0)), 2.0);
        gl_Position = projectionMatrix * mv;
      }`,
    fragmentShader: `
      uniform vec3 uColor; uniform float uTime; uniform float uSpeed;
      varying float vSeed; varying float vFade;
      void main(){
        if (vFade <= 0.001) discard;
        vec2 p = gl_PointCoord - 0.5;
        float d = length(p) * 2.0;
        if (d > 1.0) discard;
        float core = smoothstep(0.30, 0.0, d) * 1.2;
        float ph = fract(uTime * uSpeed + vSeed);
        float ring = smoothstep(0.07, 0.0, abs(d - ph)) * (1.0 - ph);
        gl_FragColor = vec4(uColor, clamp(core + ring, 0.0, 1.0) * vFade);
      }`,
  });

  function buildLandMarkers(mesh) {
    try {
      const baseTex = mesh.material.map;
      if (!baseTex || !baseTex.image) return;
      const img = baseTex.image;
      const cvs = document.createElement('canvas');
      cvs.width = img.width || 512;
      cvs.height = img.height || 256;
      const ctx = cvs.getContext('2d');
      ctx.drawImage(img, 0, 0, cvs.width, cvs.height);
      const imgData = ctx.getImageData(0, 0, cvs.width, cvs.height).data;

      const geo = mesh.geometry;
      const posAttr = geo.attributes.position;
      const uvAttr = geo.attributes.uv;
      const idxAttr = geo.index;
      if (!posAttr || !uvAttr) return;

      const triCount = idxAttr ? idxAttr.count / 3 : posAttr.count / 3;
      const areas = new Float32Array(triCount);
      const va = new THREE.Vector3(), vb = new THREE.Vector3(), vc = new THREE.Vector3();
      const ab = new THREE.Vector3(), ac = new THREE.Vector3();
      let totalArea = 0;

      for (let t = 0; t < triCount; t++) {
        const i0 = idxAttr ? idxAttr.getX(t * 3) : t * 3;
        const i1 = idxAttr ? idxAttr.getX(t * 3 + 1) : t * 3 + 1;
        const i2 = idxAttr ? idxAttr.getX(t * 3 + 2) : t * 3 + 2;
        va.fromBufferAttribute(posAttr, i0);
        vb.fromBufferAttribute(posAttr, i1);
        vc.fromBufferAttribute(posAttr, i2);
        ab.subVectors(vb, va);
        ac.subVectors(vc, va);
        totalArea += ab.cross(ac).length() * 0.5;
        areas[t] = totalArea;
      }

      const positions = [];
      const seeds = [];
      const uva = new THREE.Vector2(), uvb = new THREE.Vector2(), uvc = new THREE.Vector2();
      let attempts = 0;

      while (seeds.length < CONFIG.markerCount && attempts < CONFIG.markerCount * 40) {
        attempts++;
        const r = Math.random() * totalArea;
        let lo = 0, hi = triCount - 1;
        while (lo < hi) {
          const mid = (lo + hi) >> 1;
          if (areas[mid] < r) lo = mid + 1; else hi = mid;
        }
        const i0 = idxAttr ? idxAttr.getX(lo * 3) : lo * 3;
        const i1 = idxAttr ? idxAttr.getX(lo * 3 + 1) : lo * 3 + 1;
        const i2 = idxAttr ? idxAttr.getX(lo * 3 + 2) : lo * 3 + 2;

        let r1 = Math.random(), r2 = Math.random();
        if (r1 + r2 > 1) { r1 = 1 - r1; r2 = 1 - r2; }
        const r0 = 1 - r1 - r2;

        uva.fromBufferAttribute(uvAttr, i0);
        uvb.fromBufferAttribute(uvAttr, i1);
        uvc.fromBufferAttribute(uvAttr, i2);
        const u = uva.x * r0 + uvb.x * r1 + uvc.x * r2;
        const v = uva.y * r0 + uvb.y * r1 + uvc.y * r2;

        const px = clamp(Math.floor(((u % 1 + 1) % 1) * cvs.width), 0, cvs.width - 1);
        const py = clamp(Math.floor(((v % 1 + 1) % 1) * cvs.height), 0, cvs.height - 1);
        const idx = (py * cvs.width + px) * 4;
        const cr = imgData[idx], cg = imgData[idx + 1], cb = imgData[idx + 2];
        if (cb > cr + 6 && cb > cg + 6) continue;

        va.fromBufferAttribute(posAttr, i0);
        vb.fromBufferAttribute(posAttr, i1);
        vc.fromBufferAttribute(posAttr, i2);
        const p = new THREE.Vector3()
          .addScaledVector(va, r0)
          .addScaledVector(vb, r1)
          .addScaledVector(vc, r2)
          .multiplyScalar(1.012);
        positions.push(p.x, p.y, p.z);
        seeds.push(Math.random());
      }

      const mGeo = new THREE.BufferGeometry();
      mGeo.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
      mGeo.setAttribute('seed', new THREE.Float32BufferAttribute(seeds, 1));
      const mPoints = new THREE.Points(mGeo, markerMat);
      mPoints.layers.set(LAYERS.ENTIRE_SCENE);
      mesh.add(mPoints);
    } catch (e) {
      console.warn('Marker sampling fallback:', e);
    }
  }

  // GLTF & DRACO Loaders
  const dracoLoader = new DRACOLoader();
  dracoLoader.setDecoderPath('https://www.gstatic.com/draco/versioned/decoders/1.5.5/');
  const gltfLoader = new GLTFLoader();
  gltfLoader.setDRACOLoader(dracoLoader);

  let entryStartTime = null;
  const ENTRY_DUR = 1.9;
  const ENTRY_START_Y = -6.5;

  gltfLoader.load(PLANET_LIGHTS_GLB, (lightsGltf) => {
    let nightTex = null;
    lightsGltf.scene.traverse((child) => {
      if (child.isMesh && !nightTex) nightTex = child.material.map;
    });

    gltfLoader.load(PLANET_GLB, (planetGltf) => {
      let planetMesh = null;
      planetGltf.scene.traverse((child) => {
        if (child.isMesh && !planetMesh) planetMesh = child;
      });
      if (!planetMesh) return;

      planetMesh.geometry.computeBoundingSphere();
      const r = planetMesh.geometry.boundingSphere.radius || 1;
      planetMesh.scale.setScalar(CONFIG.planetRadius / r);

      const mat = planetMesh.material.clone();
      mat.metalness = 0;
      mat.roughness = 1;
      mat.envMapIntensity = 0;
      mat.extensions = { derivatives: true };

      mat.onBeforeCompile = (shader) => {
        shader.uniforms.time = planetTimeUniform;
        shader.uniforms.noiseScale = { value: 30.0 };
        shader.uniforms.speedX = { value: 1.5 };
        shader.uniforms.speedY = { value: 2.0 };
        shader.uniforms.speedZ = { value: 2.5 };
        shader.uniforms.rimColor = { value: hexToVec3(CONFIG.rimColor) };
        shader.uniforms.rimPower = { value: CONFIG.rimPower };
        shader.uniforms.nightBlendTexture = { value: nightTex };
        shader.uniforms.nightLights = { value: CONFIG.nightLights };
        shader.uniforms.terrainDepth = { value: CONFIG.terrainDepth };
        shader.uniforms.terrainShade = { value: CONFIG.terrainShade };
        shader.uniforms.oceanGlint = { value: CONFIG.oceanGlint };
        shader.uniforms.oceanDeep = { value: CONFIG.oceanDeep };
        shader.uniforms.oceanFlow = { value: CONFIG.oceanFlow };
        shader.uniforms.oceanFlowSpeed = { value: CONFIG.oceanFlowSpeed };
        shader.uniforms.oceanFlowScale = { value: CONFIG.oceanFlowScale };

        shader.vertexShader = `varying vec2 vCustomUv;\n` + shader.vertexShader.replace(
          `void main() {`,
          `void main() {\n vCustomUv = uv;`
        );
        shader.fragmentShader = `
          uniform float time; uniform float noiseScale; uniform float speedX; uniform float speedY; uniform float speedZ;
          uniform vec3 rimColor; uniform float rimPower; uniform sampler2D nightBlendTexture; uniform float nightLights;
          uniform float terrainDepth; uniform float terrainShade; uniform float oceanGlint; uniform float oceanDeep;
          uniform float oceanFlow; uniform float oceanFlowSpeed; uniform float oceanFlowScale;
          varying vec2 vCustomUv;
          ${SNOISE}
        ` + shader.fragmentShader.replace(
          `#include <dithering_fragment>`,
          `#include <dithering_fragment>
          vec3 normalizedNormal = normalize(vNormal);
          vec3 viewDir = normalize(vViewPosition);
          float rim = 1.0 - max(dot(viewDir, normalizedNormal), 0.0);
          rim = pow(rim, rimPower); rim = pow(rim, 1.5); rim *= 0.7;
          vec3 currentColor = gl_FragColor.rgb;
          float blueDom = currentColor.b - max(currentColor.r, currentColor.g);
          float waterMask = clamp(smoothstep(-0.005, 0.03, blueDom), 0.0, 1.0);
          float shimmer = snoise(vec3(vCustomUv.x * noiseScale + time * speedX, vCustomUv.y * noiseScale - time * speedY, time * speedZ));
          gl_FragColor.rgb += waterMask * shimmer * 0.025;
          float fT = time * oceanFlowSpeed * 4.0;
          float fS = 4.0 * oceanFlowScale;
          float warp = snoise(vec3(vCustomUv.x * fS - fT * 0.5, vCustomUv.y * fS + fT * 0.4, fT * 0.5));
          float flow = snoise(vec3(vCustomUv.x * fS * 2.0 + fT * 0.6 + warp, vCustomUv.y * fS * 2.0 - fT * 0.5, fT * 0.7));
          flow = warp * 0.6 + flow * 0.4;
          gl_FragColor.rgb += waterMask * flow * 0.12 * oceanFlow;
          gl_FragColor.rgb = mix(gl_FragColor.rgb, vec3(0.01, 0.06, 0.16), waterMask * oceanDeep);
          vec3 finalColor = mix(gl_FragColor.rgb, rimColor, rim);
          gl_FragColor = vec4(finalColor, 1.0);
          vec3 surfPos = -vViewPosition;
          float terrH = dot(texture2D(map, vCustomUv).rgb, vec3(0.299, 0.587, 0.114));
          vec3 sigX = dFdx(surfPos), sigY = dFdy(surfPos);
          vec3 vR1 = cross(sigY, normalizedNormal), vR2 = cross(normalizedNormal, sigX);
          float fDet = dot(sigX, vR1);
          vec3 vGrad = sign(fDet) * (dFdx(terrH) * vR1 + dFdy(terrH) * vR2);
          vec3 bumpedNormal = normalize(abs(fDet) * normalizedNormal - terrainDepth * vGrad);
          vec3 shadeNormal = mix(bumpedNormal, normalizedNormal, waterMask);
          vec3 cityLights = texture2D(nightBlendTexture, vCustomUv).rgb * gl_FragColor.rgb * nightLights;
          vec3 viewSunDir = normalize(vec3(-0.9, 0.18, 0.4));
          float ndl = dot(normalizedNormal, viewSunDir);
          float dayAmt = smoothstep(-0.05, 0.35, ndl);
          float relief = dot(shadeNormal, viewSunDir) - ndl;
          gl_FragColor.rgb *= clamp(1.0 + relief * terrainShade * dayAmt, 0.55, 1.6);
          float nightFactor  = smoothstep(0.18, -0.30, ndl);
          float lightsFactor = smoothstep(0.30, -0.35, ndl);
          gl_FragColor.rgb = mix(gl_FragColor.rgb, gl_FragColor.rgb * 0.08, nightFactor);
          gl_FragColor.rgb += cityLights * lightsFactor;
          vec3 halfDir = normalize(viewSunDir + viewDir);
          float ripple = snoise(vec3(vCustomUv * 240.0, time * 4.0));
          float ndh = max(dot(normalizedNormal, halfDir) + ripple * 0.02, 0.0);
          float glint = pow(ndh, 140.0);
          gl_FragColor.rgb += glint * waterMask * dayAmt * oceanGlint * vec3(1.0, 0.97, 0.88);`
        );
      };

      planetMesh.material = mat;
      planetMesh.layers.set(LAYERS.ENTIRE_SCENE);
      planetGroup.add(planetMesh);
      cloudGroup.visible = true;
      buildLandMarkers(planetMesh);
      entryStartTime = performance.now() * 0.001;
    });
  });

  // Scroll & Animation Loop
  let curP = 0, curX = STOPS_X[0].v, curY = STOPS_Y[0].v, curS = STOPS_S[0].v;
  let spinPhase = 0;
  let lastT = performance.now() * 0.001;

  function animate() {
    requestAnimationFrame(animate);
    const now = performance.now() * 0.001;
    const dt = Math.min(now - lastT, 0.05);
    lastT = now;

    planetTimeUniform.value += dt / 12;
    cloudTimeUniform.value += dt / 20;
    starMat.uniforms.uTime.value += dt;
    markerMat.uniforms.uTime.value += dt;

    cloudMeshes.forEach((c) => {
      c.mesh.rotation.y += dt * CONFIG[c.spinKey];
    });

    const maxScroll = Math.max(document.documentElement.scrollHeight - window.innerHeight, 1);
    const pTarget = clamp(window.scrollY / maxScroll, 0, 1);
    curP += (pTarget - curP) * Math.min(1, dt * 4.5);

    const sideScale = clamp(window.innerWidth / 1200, 0.5, 1);
    const targetX = sample(STOPS_X, curP) * sideScale;
    const targetY = sample(STOPS_Y, curP);
    const targetS = sample(STOPS_S, curP);

    const k = Math.min(1, dt * 3.2);
    curX += (targetX - curX) * k;
    curY += (targetY - curY) * k;
    curS += (targetS - curS) * k;

    let entryY = 0;
    if (!reducedMotion && entryStartTime !== null) {
      const t = clamp((now - entryStartTime) / ENTRY_DUR, 0, 1);
      const ease = 1 - Math.pow(1 - t, 3);
      entryY = ENTRY_START_Y * (1 - ease);
    }

    worldGroup.position.set(curX, curY + entryY, 0);
    worldGroup.scale.setScalar(curS);

    spinPhase += dt * CONFIG.spin;
    planetGroup.rotation.y = CONFIG.initRotation + spinPhase + curP * Math.PI * 1.6;
    glowMesh.quaternion.copy(camera.quaternion);

    camera.layers.set(LAYERS.TORUS_SCENE);
    torusComposer.render();
    camera.layers.set(LAYERS.BLOOM_SCENE);
    bloomComposer.render();
    camera.layers.set(LAYERS.ENTIRE_SCENE);
    finalComposer.render();
  }
  requestAnimationFrame(animate);

  window.addEventListener('resize', () => {
    w = window.innerWidth;
    h = window.innerHeight;
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setPixelRatio(window.devicePixelRatio);
    renderer.setSize(w, h);
    torusComposer.setSize(w, h);
    bloomComposer.setSize(w, h);
    finalComposer.setSize(w, h);
    atmoMat.uniforms.uRes.value.set(w, h);
    starMat.uniforms.uRes.value.set(w, h);
    markerMat.uniforms.uRes.value.set(w, h);
  });
}
